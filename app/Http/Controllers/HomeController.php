<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Admin không xem trang khách hàng — về thẳng trang quản trị
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Lấy danh sách các môn thể thao đang hoạt động
        $sports = Sport::where('is_active', true)->get();

        // Khu sân nổi bật — xếp hạng theo lượt đặt thật (trừ đơn hủy), phụ trợ điểm đánh giá
        $bookingCounts = Venue::bookingCountsByVenue();

        $featuredVenues = Venue::whereIn('status', ['active', 'approved'])
            ->with(['courts' => fn ($q) => $q->where('status', 'active')->with(['sport', 'slots']), 'reviews'])
            ->withCount([
                'courts as courts_count' => fn ($q) => $q->where('status', 'active'),
                'reviews as reviews_count',
            ])
            ->get()
            ->each(fn ($v) => $v->bookings_count = $bookingCounts[$v->id] ?? 0)
            ->sortBy([
                fn ($a, $b) => $b->bookings_count <=> $a->bookings_count,
                fn ($a, $b) => (float) $b->rating_avg <=> (float) $a->rating_avg,
                fn ($a, $b) => $b->reviews_count <=> $a->reviews_count,
            ])
            ->values()
            ->take(5);

        // Banner cho hero carousel — admin có thể thay ảnh sau bằng cách cập nhật `image_url`
        $banners = [
            [
                'eyebrow'   => 'Đặt sân thể thao trực tuyến',
                'title'     => 'Sân chất lượng, thao tác trong 60 giây',
                'subtitle'  => 'Hơn 200 khu sân bóng đá, cầu lông, tennis và pickleball được kiểm định trên toàn quốc.',
                'cta_label' => 'Tìm sân ngay',
                'cta_href'  => '/search',
                'image_url' => asset('images/banners/banner_3.jpg'),
                'theme'     => 'cobalt',
            ],
            [
                'eyebrow'   => 'Ưu đãi tuần này',
                'title'     => 'Giảm 20% cho lần đặt sân đầu tiên',
                'subtitle'  => 'Áp dụng tự động khi đăng ký thành viên Arena và thanh toán trực tuyến.',
                'cta_label' => 'Đăng ký thành viên',
                'cta_href'  => '/register',
                'image_url' => asset('images/banners/banner_2.jpg'),
                'theme'     => 'emerald',
            ],
            [
                'eyebrow'   => 'Dành cho chủ sân',
                'title'     => 'Quản lý lịch đặt và doanh thu ở một nơi',
                'subtitle'  => 'Kết nối với hàng nghìn người chơi, theo dõi công suất sân theo thời gian thực.',
                'cta_label' => 'Trở thành đối tác',
                'cta_href'  => '/register',
                'image_url' => asset('images/banners/banner_1.jpg'),
                'theme'     => 'graphite',
            ],
        ];

        // Tiện ích giúp người dùng hiểu nhanh giá trị cốt lõi
        $valueProps = [
            [
                'title' => 'Xác nhận tức thì',
                'desc'  => 'Đặt sân và nhận xác nhận trong vòng một phút, không cần gọi điện.',
            ],
            [
                'title' => 'Thanh toán an toàn',
                'desc'  => 'Hỗ trợ ví điện tử, thẻ nội địa và chuyển khoản với hóa đơn điện tử.',
            ],
            [
                'title' => 'Linh hoạt thời gian',
                'desc'  => 'Hủy miễn phí trước 6 giờ, đổi khung giờ chỉ với một thao tác.',
            ],
        ];

        $latestNews = NewsArticle::query()
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        return view('home', compact('sports', 'featuredVenues', 'banners', 'valueProps', 'latestNews'));
    }
}
