<?php

namespace App\Console\Commands;

use App\Models\NewsArticle;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

#[Signature('news:fetch')]
#[Description('Thu thập và cập nhật tin thể thao từ RSS của VnExpress và Thanh Niên')]
class FetchNewsArticles extends Command
{
    /**
     * @var array<string, string>
     */
    private const FEEDS = [
        'VnExpress' => 'https://vnexpress.net/rss/the-thao.rss',
        'Thanh Niên' => 'https://thanhnien.vn/rss/the-thao.rss',
    ];

    public function handle(): int
    {
        $created = 0;
        $updated = 0;

        foreach (self::FEEDS as $source => $url) {
            try {
                $response = Http::timeout(10)->get($url);
            } catch (ConnectionException $exception) {
                // Một nguồn lỗi mạng không được làm gián đoạn việc đọc nguồn còn lại.
                $this->warn("Không thể kết nối RSS {$source}: {$exception->getMessage()}");

                continue;
            }

            if (! $response->successful()) {
                $this->warn("Không thể đọc RSS {$source} (HTTP {$response->status()}).");

                continue;
            }

            $items = $this->parseFeed($response->body());

            if ($items === null) {
                $this->warn("RSS {$source} không phải XML hợp lệ.");

                continue;
            }

            foreach ($items as $item) {
                $link = trim((string) $item->link);
                $title = trim((string) $item->title);

                if (! $this->isValidArticleUrl($link) || $title === '') {
                    continue;
                }

                $description = (string) $item->description;
                $article = NewsArticle::updateOrCreate(
                    ['source_url' => $link],
                    [
                        'title' => Str::limit(html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 255, ''),
                        'source' => $source,
                        'thumbnail' => $this->extractThumbnail($description),
                        'summary' => $this->extractSummary($description),
                        'published_at' => $this->publishedAt($item),
                    ],
                );

                $article->wasRecentlyCreated ? $created++ : $updated++;
            }
        }

        $this->info("Hoàn tất: {$created} bài mới, {$updated} bài được cập nhật.");

        return self::SUCCESS;
    }

    /**
     * @return list<SimpleXMLElement>|null
     */
    private function parseFeed(string $contents): ?array
    {
        $previousState = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_string($contents, SimpleXMLElement::class, LIBXML_NOCDATA | LIBXML_NONET);

            if ($xml === false || ! isset($xml->channel->item)) {
                return null;
            }

            return iterator_to_array($xml->channel->item, false);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousState);
        }
    }

    private function extractThumbnail(string $description): ?string
    {
        $description = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (! preg_match('/<img\b[^>]*\b(?:src|data-src)\s*=\s*(["\'])(.*?)\1/is', $description, $matches)) {
            return null;
        }

        $thumbnail = trim(html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if (str_starts_with($thumbnail, '//')) {
            $thumbnail = 'https:'.$thumbnail;
        }

        return $this->isValidHttpUrl($thumbnail) && Str::length($thumbnail) <= 255 ? $thumbnail : null;
    }

    private function extractSummary(string $description): ?string
    {
        $withoutImages = preg_replace('/<img\b[^>]*>/is', ' ', $description) ?? $description;
        $summary = Str::squish(html_entity_decode(strip_tags($withoutImages), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $summary !== '' ? $summary : null;
    }

    private function publishedAt(SimpleXMLElement $item): Carbon
    {
        $publishedAt = trim((string) $item->pubDate);

        if ($publishedAt === '') {
            return now();
        }

        try {
            return Carbon::parse($publishedAt);
        } catch (Throwable) {
            return now();
        }
    }

    private function isValidArticleUrl(string $url): bool
    {
        return Str::length($url) <= 255 && $this->isValidHttpUrl($url);
    }

    private function isValidHttpUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
