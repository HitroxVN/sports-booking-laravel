<?php

namespace Tests\Feature\Customer;

use App\Http\Middleware\RunScheduler;
use App\Models\NewsArticle;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_fetch_imports_both_feeds_and_does_not_create_duplicates(): void
    {
        Http::fake([
            'https://vnexpress.net/rss/the-thao.rss' => Http::response($this->vnExpressFeed()),
            'https://thanhnien.vn/rss/the-thao.rss' => Http::response($this->thanhNienFeed()),
        ]);

        $this->artisan('news:fetch')->assertSuccessful();

        $this->assertDatabaseCount('news_articles', 2);
        $this->assertDatabaseHas('news_articles', [
            'source' => 'VnExpress',
            'source_url' => 'https://vnexpress.net/the-thao/bai-viet-1.html',
            'thumbnail' => 'https://i1-thethao.vnecdn.net/anh-bong-da.jpg?x=1&y=2',
            'summary' => 'Đội tuyển Việt Nam giành chiến thắng thuyết phục.',
        ]);
        $this->assertDatabaseHas('news_articles', [
            'title' => 'Ngôi sao tỏa sáng',
            'source' => 'Thanh Niên',
            'source_url' => 'https://thanhnien.vn/ngoi-sao-toa-sang-185260926230144379.htm',
            'thumbnail' => 'https://images2.thanhnien.vn/zoom/600_315/anh-bong-da.jpg',
            'summary' => 'Màn trình diễn đáng chú ý trong trận cầu tâm điểm.',
        ]);

        $article = NewsArticle::where('source', 'VnExpress')->firstOrFail();
        $this->assertInstanceOf(Carbon::class, $article->published_at);

        $this->artisan('news:fetch')->assertSuccessful();

        $this->assertDatabaseCount('news_articles', 2);
        Http::assertSentCount(4);
    }

    public function test_news_fetch_continues_when_one_source_has_a_network_error(): void
    {
        Http::fake(function ($request) {
            if ($request->url() === 'https://vnexpress.net/rss/the-thao.rss') {
                throw new ConnectionException('Mất kết nối');
            }

            return Http::response($this->thanhNienFeed());
        });

        $this->artisan('news:fetch')
            ->expectsOutputToContain('Không thể kết nối RSS VnExpress')
            ->assertSuccessful();

        $this->assertDatabaseCount('news_articles', 1);
        $this->assertDatabaseHas('news_articles', ['source' => 'Thanh Niên']);
    }

    public function test_news_index_is_public_and_paginates_twelve_articles(): void
    {
        $this->withoutMiddleware(RunScheduler::class);

        foreach (range(1, 13) as $index) {
            $this->createArticle("Tin số {$index}", now()->subMinutes($index));
        }

        $response = $this->get(route('customer.news.index'));

        $response->assertOk()
            ->assertViewHas('articles', fn ($articles) => $articles->count() === 12 && $articles->total() === 13)
            ->assertSee('grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6', false)
            ->assertSee(asset('images/defaults/news.svg'), false)
            ->assertSee('Xem bài viết gốc')
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertSee('page=2', false);
    }

    public function test_homepage_displays_only_four_latest_articles(): void
    {
        $this->withoutMiddleware(RunScheduler::class);

        foreach (range(1, 5) as $index) {
            $this->createArticle("Tin mới {$index}", now()->subMinutes($index));
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Xem tất cả tin tức')
            ->assertSee('Tin mới 1')
            ->assertSee('Tin mới 4')
            ->assertDontSee('Tin mới 5');
    }

    public function test_news_fetch_is_scheduled_every_thirty_minutes(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn ($event) => str_contains($event->command ?? '', 'news:fetch'));

        $this->assertNotNull($event);
        $this->assertSame('*/30 * * * *', $event->expression);
    }

    private function createArticle(string $title, Carbon $publishedAt): NewsArticle
    {
        return NewsArticle::create([
            'title' => $title,
            'source' => 'VnExpress',
            'source_url' => 'https://example.com/'.str($title)->slug(),
            'thumbnail' => null,
            'summary' => 'Bản tin thể thao mới nhất.',
            'published_at' => $publishedAt,
        ]);
    }

    private function vnExpressFeed(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0">
                <channel>
                    <title>VnExpress Thể thao</title>
                    <item>
                        <title><![CDATA[Việt Nam thắng trận mở màn]]></title>
                        <link>https://vnexpress.net/the-thao/bai-viet-1.html</link>
                        <description><![CDATA[<img src="https://i1-thethao.vnecdn.net/anh-bong-da.jpg?x=1&amp;y=2"> Đội tuyển Việt Nam giành chiến thắng thuyết phục.]]></description>
                        <pubDate>Fri, 25 Sep 2026 10:30:00 +0700</pubDate>
                    </item>
                </channel>
            </rss>
            XML;
    }

    private function thanhNienFeed(): string
    {
        return <<<'XML'
            <?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0">
                <channel>
                    <title>Thể thao | Báo Thanh Niên</title>
                    <item>
                        <title><![CDATA[Ng&ocirc;i sao tỏa s&aacute;ng]]></title>
                        <link>https://thanhnien.vn/ngoi-sao-toa-sang-185260926230144379.htm</link>
                        <description><![CDATA[<a href="https://thanhnien.vn/ngoi-sao-toa-sang-185260926230144379.htm"><img src="https://images2.thanhnien.vn/zoom/600_315/anh-bong-da.jpg"></a> M&agrave;n tr&igrave;nh diễn đ&aacute;ng ch&uacute; &yacute; trong trận cầu t&acirc;m điểm.]]></description>
                        <pubDate>Fri, 25 Sep 2026 11:00:00 +0700</pubDate>
                    </item>
                </channel>
            </rss>
            XML;
    }
}
