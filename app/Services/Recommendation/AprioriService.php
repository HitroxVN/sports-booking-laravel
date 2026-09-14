<?php

namespace App\Services\Recommendation;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Venue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AprioriService
{
    protected float $minSupport;
    protected float $minConfidence;

    public function __construct(float $minSupport = 0.15, float $minConfidence = 0.3)
    {
        $this->minSupport = $minSupport;
        $this->minConfidence = $minConfidence;
    }

    /**
     * Trích xuất các giỏ giao dịch (Transactions) từ lịch sử đơn hàng
     *
     * @return array<int, array<int>> Danh sách các giỏ (mỗi giỏ là mảng các court_id phân biệt)
     */
    public function extractTransactions(): array
    {
        // 1. Nhóm theo user_id (những sân mà 1 khách hàng đã từng đặt)
        $userTransactions = Booking::select('user_id', DB::raw('GROUP_CONCAT(DISTINCT court_id) as court_ids'))
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('user_id')
            ->pluck('court_ids')
            ->map(fn ($ids) => array_values(array_unique(array_map('intval', explode(',', $ids)))))
            ->filter(fn ($basket) => count($basket) >= 1)
            ->values()
            ->toArray();

        // 2. Nhóm theo (user_id, booking_date) để nắm bắt hành vi đặt nhiều sân trong cùng 1 ngày
        $dateTransactions = Booking::select('user_id', 'booking_date', DB::raw('GROUP_CONCAT(DISTINCT court_id) as court_ids'))
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->groupBy('user_id', 'booking_date')
            ->havingRaw('COUNT(DISTINCT court_id) >= 2')
            ->pluck('court_ids')
            ->map(fn ($ids) => array_values(array_unique(array_map('intval', explode(',', $ids)))))
            ->values()
            ->toArray();

        $allTransactions = array_merge($userTransactions, $dateTransactions);

        // Trường hợp hệ thống mới chưa đủ giao dịch, fallback lấy tất cả booking theo user
        if (empty($allTransactions)) {
            $allTransactions = Booking::select('user_id', DB::raw('GROUP_CONCAT(DISTINCT court_id) as court_ids'))
                ->groupBy('user_id')
                ->pluck('court_ids')
                ->map(fn ($ids) => array_values(array_unique(array_map('intval', explode(',', $ids)))))
                ->values()
                ->toArray();
        }

        return $allTransactions;
    }

    /**
     * Khai phá dữ liệu với thuật toán Apriori
     *
     * @return array{frequent_itemsets: array, rules: array, total_transactions: int}
     */
    public function mine(?array $transactions = null): array
    {
        $transactions = $transactions ?? $this->extractTransactions();
        $totalTransactions = count($transactions);

        if ($totalTransactions === 0) {
            return [
                'frequent_itemsets'  => [],
                'rules'              => [],
                'total_transactions' => 0,
            ];
        }

        // Bước 1: Tìm tập 1-phần tử phổ biến L1
        $itemCounts = [];
        foreach ($transactions as $basket) {
            foreach ($basket as $item) {
                $itemCounts[$item] = ($itemCounts[$item] ?? 0) + 1;
            }
        }

        $frequentItemsets = [];
        $itemSupportMap = [];

        // L1
        $currentL = [];
        foreach ($itemCounts as $item => $count) {
            $support = $count / $totalTransactions;
            if ($support >= $this->minSupport) {
                $itemset = [$item];
                $key = (string) $item;
                $currentL[$key] = [
                    'items'   => $itemset,
                    'count'   => $count,
                    'support' => round($support, 4),
                ];
                $itemSupportMap[$key] = $support;
            }
        }

        $k = 1;
        while (!empty($currentL)) {
            $frequentItemsets[$k] = $currentL;
            $k++;

            // Bước 2: Sinh ứng viên C_k từ L_{k-1}
            $prevItemsets = array_values(array_map(fn ($entry) => $entry['items'], $currentL));
            $candidates = $this->generateCandidates($prevItemsets, $k);

            if (empty($candidates)) {
                break;
            }

            // Bước 3: Đếm tần suất ứng viên
            $candidateCounts = array_fill(0, count($candidates), 0);
            foreach ($transactions as $basket) {
                $basketFlip = array_flip($basket);
                foreach ($candidates as $idx => $candidate) {
                    $match = true;
                    foreach ($candidate as $cItem) {
                        if (!isset($basketFlip[$cItem])) {
                            $match = false;
                            break;
                        }
                    }
                    if ($match) {
                        $candidateCounts[$idx]++;
                    }
                }
            }

            // Lọc ứng viên đạt minSupport để tạo L_k
            $currentL = [];
            foreach ($candidates as $idx => $candidate) {
                $support = $candidateCounts[$idx] / $totalTransactions;
                if ($support >= $this->minSupport) {
                    sort($candidate);
                    $key = implode(',', $candidate);
                    $currentL[$key] = [
                        'items'   => $candidate,
                        'count'   => $candidateCounts[$idx],
                        'support' => round($support, 4),
                    ];
                    $itemSupportMap[$key] = $support;
                }
            }
        }

        // Bước 4: Sinh các Luật kết hợp (Association Rules: A => B)
        $rules = [];
        foreach ($frequentItemsets as $size => $itemsets) {
            if ($size < 2) continue;

            foreach ($itemsets as $key => $data) {
                $items = $data['items'];
                $itemsetSupport = $data['support'];

                // Sinh tất cả tập con khác rỗng (antecedents)
                $subsets = $this->getSubsets($items);
                foreach ($subsets as $antecedent) {
                    $consequent = array_values(array_diff($items, $antecedent));
                    if (empty($consequent)) continue;

                    sort($antecedent);
                    sort($consequent);

                    $antKey = implode(',', $antecedent);
                    $conKey = implode(',', $consequent);

                    $antSupport = $itemSupportMap[$antKey] ?? 0;
                    $conSupport = $itemSupportMap[$conKey] ?? 0;

                    if ($antSupport > 0) {
                        $confidence = $itemsetSupport / $antSupport;
                        if ($confidence >= $this->minConfidence) {
                            $lift = ($conSupport > 0) ? ($confidence / $conSupport) : 1;

                            $rules[] = [
                                'antecedent' => $antecedent,
                                'consequent' => $consequent,
                                'support'    => round($itemsetSupport, 4),
                                'confidence' => round($confidence, 4),
                                'lift'       => round($lift, 4),
                            ];
                        }
                    }
                }
            }
        }

        // Sắp xếp luật theo Lift giảm dần rồi đến Confidence giảm dần
        usort($rules, function ($a, $b) {
            if ($b['lift'] === $a['lift']) {
                return $b['confidence'] <=> $a['confidence'];
            }
            return $b['lift'] <=> $a['lift'];
        });

        return [
            'frequent_itemsets'  => $frequentItemsets,
            'rules'              => $rules,
            'total_transactions' => $totalTransactions,
        ];
    }

    /**
     * Lấy danh sách luật đã cache hoặc thực hiện khai phá mới
     */
    public function getCachedRules(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget('apriori_recommendation_rules');
        }

        return Cache::remember('apriori_recommendation_rules', now()->addHours(6), function () {
            return $this->mine();
        });
    }

    /**
     * Gợi ý danh sách sân cho 1 sân cụ thể (Court Recommendation)
     *
     * @param int $courtId
     * @param int $limit
     * @return Collection
     */
    public function getRecommendationsForCourt(int $courtId, int $limit = 4): Collection
    {
        $currentCourt = Court::with(['venue', 'sport'])->find($courtId);
        if (!$currentCourt) {
            return collect();
        }

        $mined = $this->getCachedRules();
        $rules = $mined['rules'] ?? [];

        $recommendedCourtScores = [];

        foreach ($rules as $rule) {
            // Kiểm tra xem tiền đề antecedent có chứa courtId hiện tại không
            if (in_array($courtId, $rule['antecedent'])) {
                foreach ($rule['consequent'] as $consequentCourtId) {
                    if ($consequentCourtId === $courtId) continue;

                    $score = $rule['lift'] * 10 + $rule['confidence'] * 5 + $rule['support'];
                    if (!isset($recommendedCourtScores[$consequentCourtId]) || $recommendedCourtScores[$consequentCourtId]['score'] < $score) {
                        $recommendedCourtScores[$consequentCourtId] = [
                            'court_id'   => $consequentCourtId,
                            'score'      => $score,
                            'confidence' => $rule['confidence'],
                            'lift'       => $rule['lift'],
                            'support'    => $rule['support'],
                            'reason'     => 'Khách đặt sân này thường chọn thêm',
                            'badge'      => 'Thường đặt cùng',
                        ];
                    }
                }
            }
        }

        // Sắp xếp theo score giảm dần
        uasort($recommendedCourtScores, fn ($a, $b) => $b['score'] <=> $a['score']);
        $recommendedCourtIds = array_keys($recommendedCourtScores);

        // Lấy thông tin các sân được gợi ý từ DB
        $recommendedCourts = Court::whereIn('id', $recommendedCourtIds)
            ->where('status', 'active')
            ->with(['venue', 'sport', 'slots'])
            ->get()
            ->keyBy('id');

        $results = collect();
        foreach ($recommendedCourtScores as $id => $meta) {
            if ($court = $recommendedCourts->get($id)) {
                $court->recommendation_meta = $meta;
                $results->push($court);
                if ($results->count() >= $limit) break;
            }
        }

        // Nếu chưa đủ $limit (cold-start hoặc ít giao dịch tương quan):
        // Fallback thông minh: Gợi ý các sân cùng môn thể thao hoặc cùng khu sân có rating cao
        if ($results->count() < $limit) {
            $excludedIds = $results->pluck('id')->push($courtId)->toArray();

            $fallbackCourts = Court::whereNotIn('id', $excludedIds)
                ->where('status', 'active')
                ->where(function ($q) use ($currentCourt) {
                    $q->where('sport_id', $currentCourt->sport_id)
                      ->orWhere('venue_id', $currentCourt->venue_id);
                })
                ->with(['venue', 'sport', 'slots'])
                ->take($limit - $results->count())
                ->get();

            foreach ($fallbackCourts as $fCourt) {
                $fCourt->recommendation_meta = [
                    'court_id'   => $fCourt->id,
                    'score'      => 1.0,
                    'confidence' => 0.5,
                    'lift'       => 1.0,
                    'support'    => 0.2,
                    'reason'     => $fCourt->sport_id === $currentCourt->sport_id ? 'Cùng môn thể thao yêu thích' : 'Cùng địa điểm thuận tiện',
                    'badge'      => 'Gợi ý nổi bật',
                ];
                $results->push($fCourt);
            }
        }

        return $results;
    }

    /**
     * Gợi ý danh sách sân cho 1 khu sân (Venue Recommendation)
     *
     * @param int $venueId
     * @param int $limit
     * @return Collection
     */
    public function getRecommendationsForVenue(int $venueId, int $limit = 4): Collection
    {
        $venue = Venue::with('courts')->find($venueId);
        if (!$venue || $venue->courts->isEmpty()) {
            return collect();
        }

        $courtIds = $venue->courts->pluck('id')->toArray();
        $recommendations = collect();

        foreach ($courtIds as $cId) {
            $subRecs = $this->getRecommendationsForCourt($cId, 2);
            foreach ($subRecs as $rec) {
                if ($rec->venue_id !== $venueId && !$recommendations->contains('id', $rec->id)) {
                    $recommendations->push($rec);
                }
            }
            if ($recommendations->count() >= $limit) break;
        }

        if ($recommendations->count() < $limit) {
            // Fallback lấy các sân active từ khu sân khác
            $fallbacks = Court::where('venue_id', '!=', $venueId)
                ->where('status', 'active')
                ->with(['venue', 'sport', 'slots'])
                ->take($limit - $recommendations->count())
                ->get();

            foreach ($fallbacks as $fCourt) {
                $fCourt->recommendation_meta = [
                    'court_id'   => $fCourt->id,
                    'score'      => 1.0,
                    'confidence' => 0.4,
                    'lift'       => 1.0,
                    'support'    => 0.15,
                    'reason'     => 'Khu sân được ưa chuộng lân cận',
                    'badge'      => 'Có thể bạn thích',
                ];
                $recommendations->push($fCourt);
            }
        }

        return $recommendations->take($limit);
    }

    /**
     * Sinh ứng viên C_k từ L_{k-1}
     */
    protected function generateCandidates(array $prevItemsets, int $k): array
    {
        $candidates = [];
        $count = count($prevItemsets);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $itemset1 = $prevItemsets[$i];
                $itemset2 = $prevItemsets[$j];

                // Ghép nếu $k-2 phần tử đầu giống nhau
                $match = true;
                for ($m = 0; $m < $k - 2; $m++) {
                    if ($itemset1[$m] !== $itemset2[$m]) {
                        $match = false;
                        break;
                    }
                }

                if ($match) {
                    $candidate = array_values(array_unique(array_merge($itemset1, $itemset2)));
                    if (count($candidate) === $k) {
                        sort($candidate);
                        // Apriori pruning: mọi tập con kích thước k-1 phải nằm trong prevItemsets
                        if ($this->hasAllSubsetsInPrev($candidate, $prevItemsets, $k - 1)) {
                            $candidates[] = $candidate;
                        }
                    }
                }
            }
        }

        return $candidates;
    }

    /**
     * Kiểm tra xem mọi tập con cấp k-1 có thuộc L_{k-1} không
     */
    protected function hasAllSubsetsInPrev(array $candidate, array $prevItemsets, int $subsetSize): bool
    {
        $prevMap = [];
        foreach ($prevItemsets as $set) {
            sort($set);
            $prevMap[implode(',', $set)] = true;
        }

        $subsets = $this->getSubsetsOfSize($candidate, $subsetSize);
        foreach ($subsets as $sub) {
            sort($sub);
            if (!isset($prevMap[implode(',', $sub)])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lấy các tập con có kích thước chỉ định
     */
    protected function getSubsetsOfSize(array $items, int $size): array
    {
        if ($size === 0) return [[]];
        if (empty($items)) return [];

        $head = $items[0];
        $tail = array_slice($items, 1);

        $withHead = [];
        foreach ($this->getSubsetsOfSize($tail, $size - 1) as $subset) {
            $withHead[] = array_merge([$head], $subset);
        }

        $withoutHead = $this->getSubsetsOfSize($tail, $size);

        return array_merge($withHead, $withoutHead);
    }

    /**
     * Lấy tất cả tập con thực sự khác rỗng và khác tập ban đầu
     */
    protected function getSubsets(array $items): array
    {
        $subsets = [[]];
        foreach ($items as $item) {
            foreach ($subsets as $sub) {
                $subsets[] = array_merge($sub, [$item]);
            }
        }

        // Bỏ tập rỗng và tập chính nó
        $fullKey = implode(',', $items);
        return array_values(array_filter($subsets, function ($s) use ($items, $fullKey) {
            return !empty($s) && count($s) < count($items);
        }));
    }
}
