<?php

namespace App\Console\Commands;

use App\Models\Court;
use App\Services\Recommendation\AprioriService;
use Illuminate\Console\Command;

class MineRecommendationRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendation:mine-rules {--min-support=0.1} {--min-confidence=0.3} {--test-court=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Khai phá luật kết hợp (Apriori Association Rules) từ lịch sử đơn hàng và cập nhật cache gợi ý';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minSupport = (float) $this->option('min-support');
        $minConfidence = (float) $this->option('min-confidence');

        $this->info("=== KHAI PHÁ LUẬT KẾT HỢP (APRIORI ASSOCIATION RULES) ===");
        $this->line("Ngưỡng Min Support:    {$minSupport}");
        $this->line("Ngưỡng Min Confidence: {$minConfidence}");

        $service = new AprioriService($minSupport, $minConfidence);

        $transactions = $service->extractTransactions();
        $this->info("Số lượng giỏ giao dịch trích xuất được: " . count($transactions));

        $courtNames = Court::pluck('name', 'id')->toArray();

        $result = $service->mine($transactions);
        $rules = $result['rules'];

        $this->newLine();
        $this->info("1. CÁC TẬP PHỔ BIẾN (FREQUENT ITEMSETS):");
        foreach ($result['frequent_itemsets'] as $k => $itemsets) {
            $this->comment("--- Tập {$k}-phần tử (" . count($itemsets) . " tập) ---");
            $rows = [];
            foreach ($itemsets as $set) {
                $names = array_map(fn ($id) => ($courtNames[$id] ?? "Sân #{$id}") . " [ID: {$id}]", $set['items']);
                $rows[] = [
                    implode(' + ', $names),
                    $set['count'],
                    round($set['support'] * 100, 1) . '%',
                ];
            }
            $this->table(['Tập mục', 'Số lần xuất hiện', 'Support'], $rows);
        }

        $this->newLine();
        $this->info("2. DANH SÁCH LUẬT KẾT HỢP ĐƯỢC SINH RA (" . count($rules) . " LUẬT):");
        if (empty($rules)) {
            $this->warn("Không có luật nào đạt đủ ngưỡng Support & Confidence.");
        } else {
            $ruleRows = [];
            foreach ($rules as $idx => $r) {
                $antNames = implode(', ', array_map(fn ($id) => ($courtNames[$id] ?? "#{$id}"), $r['antecedent']));
                $conNames = implode(', ', array_map(fn ($id) => ($courtNames[$id] ?? "#{$id}"), $r['consequent']));
                $ruleRows[] = [
                    $idx + 1,
                    "{$antNames}  =>  {$conNames}",
                    round($r['support'] * 100, 1) . '%',
                    round($r['confidence'] * 100, 1) . '%',
                    $r['lift'],
                ];
            }
            $this->table(['STT', 'Luật (A => B)', 'Support', 'Confidence', 'Lift'], $ruleRows);
        }

        // Cập nhật Cache
        $service->getCachedRules(forceRefresh: true);
        $this->info("✓ Đã cập nhật cache luật kết hợp vào hệ thống (thời hạn: 6 giờ).");

        // Test gợi ý cho sân
        $testCourtId = (int) $this->option('test-court');
        $this->newLine();
        $this->info("3. THỬ NGHIỆM GỢI Ý CHO SÂN ID = {$testCourtId} (" . ($courtNames[$testCourtId] ?? 'Không rõ') . "):");
        $recs = $service->getRecommendationsForCourt($testCourtId, 4);

        if ($recs->isEmpty()) {
            $this->warn("Không tìm thấy gợi ý.");
        } else {
            $recRows = [];
            foreach ($recs as $r) {
                $meta = $r->recommendation_meta ?? [];
                $recRows[] = [
                    $r->id,
                    $r->name,
                    $r->venue->name ?? 'N/A',
                    $r->sport->name ?? 'N/A',
                    number_format($r->slots->min('price') ?? $r->price_snapshot ?? 0) . ' đ',
                    $meta['badge'] ?? 'Gợi ý',
                    $meta['reason'] ?? '',
                    $meta['confidence'] ?? '-',
                    $meta['lift'] ?? '-',
                ];
            }
            $this->table(['ID', 'Tên sân', 'Khu sân', 'Môn', 'Giá từ', 'Huy hiệu', 'Lý do', 'Confidence', 'Lift'], $recRows);
        }

        return Command::SUCCESS;
    }
}
