<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Recommendation\AprioriService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    protected AprioriService $aprioriService;

    public function __construct(AprioriService $aprioriService)
    {
        $this->aprioriService = $aprioriService;
    }

    /**
     * API gợi ý sản phẩm/sân "Có thể bạn cũng thích" dựa trên thuật toán Apriori
     *
     * Params:
     * - court_id: ID sân hiện tại
     * - venue_id: ID khu sân hiện tại
     * - limit: Số lượng gợi ý (mặc định 4)
     */
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 4);
        if ($limit <= 0 || $limit > 20) {
            $limit = 4;
        }

        $recommendations = collect();
        $sourceType = 'generic';
        $sourceId = null;

        if ($request->filled('court_id')) {
            $sourceType = 'court';
            $sourceId = (int) $request->court_id;
            $recommendations = $this->aprioriService->getRecommendationsForCourt($sourceId, $limit);
        } elseif ($request->filled('venue_id')) {
            $sourceType = 'venue';
            $sourceId = (int) $request->venue_id;
            $recommendations = $this->aprioriService->getRecommendationsForVenue($sourceId, $limit);
        } else {
            // Trường hợp không truyền ID: lấy gợi ý cho sân phổ biến đầu tiên
            $firstCourt = \App\Models\Court::where('status', 'active')->first();
            if ($firstCourt) {
                $sourceType = 'court';
                $sourceId = $firstCourt->id;
                $recommendations = $this->aprioriService->getRecommendationsForCourt($firstCourt->id, $limit);
            }
        }

        $data = $recommendations->map(function ($court) {
            $meta = $court->recommendation_meta ?? [];
            $minPrice = $court->slots->min('price') ?? $court->price_snapshot ?? 0;

            return [
                'id'             => $court->id,
                'name'           => $court->name,
                'sport'          => [
                    'id'   => $court->sport->id ?? null,
                    'name' => $court->sport->name ?? '',
                    'icon' => $court->sport->icon ?? null,
                ],
                'venue'          => [
                    'id'      => $court->venue->id ?? null,
                    'name'    => $court->venue->name ?? '',
                    'slug'    => $court->venue->slug ?? '',
                    'address' => $court->venue->address ?? '',
                ],
                'surface_type'   => $court->surface_type,
                'max_players'    => $court->max_players,
                'image_url'      => $court->image ? asset('storage/' . $court->image) : asset('images/defaults/court.jpg'),
                'min_price'      => (float) $minPrice,
                'formatted_price'=> number_format($minPrice, 0, ',', '.') . ' đ/giờ',
                'booking_url'    => route('customer.bookings.create', $court->id),
                'venue_url'      => $court->venue ? route('venues.show', $court->venue->slug) : '#',
                'rule_metric'    => [
                    'confidence' => $meta['confidence'] ?? null,
                    'lift'       => $meta['lift'] ?? null,
                    'support'    => $meta['support'] ?? null,
                    'score'      => $meta['score'] ?? null,
                ],
                'reason'         => $meta['reason'] ?? 'Gợi ý cho bạn',
                'badge'          => $meta['badge'] ?? 'Có thể bạn thích',
            ];
        });

        return response()->json([
            'success'     => true,
            'algorithm'   => 'Apriori Association Rules Mining',
            'source_type' => $sourceType,
            'source_id'   => $sourceId,
            'total'       => $data->count(),
            'data'        => $data,
        ]);
    }
}
