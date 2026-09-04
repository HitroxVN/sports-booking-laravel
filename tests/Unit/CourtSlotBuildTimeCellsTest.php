<?php

namespace Tests\Unit;

use App\Models\CourtSlot;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CourtSlotBuildTimeCellsTest extends TestCase
{
    private const DATE_MONDAY = '2026-09-07'; // Thứ Hai
    private const DATE_SUNDAY = '2026-09-06'; // Chủ Nhật

    #[Test]
    public function splits_round_hour_frame_into_one_hour_cells(): void
    {
        // Khung tròn giờ: 6:00 - 8:00, giá 200k/h → 2 ô 200k
        $slots = collect([
            $this->makeSlot('06:00', '08:00', 200000),
        ]);

        $cells = CourtSlot::buildTimeCells($slots, self::DATE_MONDAY);

        $this->assertCount(2, $cells);
        $this->assertEquals('06:00', $cells[0]['start']);
        $this->assertEquals('07:00', $cells[0]['end']);
        $this->assertEquals(200000.0, $cells[0]['price']);
        $this->assertEquals('07:00', $cells[1]['start']);
        $this->assertEquals('08:00', $cells[1]['end']);
        $this->assertEquals(200000.0, $cells[1]['price']);
    }

    #[Test]
    public function splits_half_open_frame_with_odd_tail_cell(): void
    {
        // Khung lẻ: 6:30 - 8:00, giá 250k/h → ô 6:30-7:30 (250k) + ô 7:30-8:00 (125k = 30 phút)
        $slots = collect([
            $this->makeSlot('06:30', '08:00', 250000),
        ]);

        $cells = CourtSlot::buildTimeCells($slots, self::DATE_MONDAY);

        $this->assertCount(2, $cells);
        $this->assertEquals('06:30', $cells[0]['start']);
        $this->assertEquals('07:30', $cells[0]['end']);
        $this->assertEquals(250000.0, $cells[0]['price']);
        $this->assertEquals('07:30', $cells[1]['start']);
        $this->assertEquals('08:00', $cells[1]['end']);
        $this->assertEquals(125000.0, $cells[1]['price']);
        $this->assertFalse($cells[1]['is_full_hour']);
    }

    #[Test]
    public function merges_contiguous_frames_into_continuous_cells(): void
    {
        // 2 khung liền nhau 6:00-7:30 (200k) + 7:30-9:00 (300k)
        // → 3 ô: 6:00-7:00 (200k), 7:00-7:30 (100k), 7:30-8:30 (300k), 8:30-9:00 (150k)
        $slots = collect([
            $this->makeSlot('06:00', '07:30', 200000),
            $this->makeSlot('07:30', '09:00', 300000),
        ]);

        $cells = CourtSlot::buildTimeCells($slots, self::DATE_MONDAY);

        $this->assertCount(4, $cells);
        $this->assertEquals(['06:00', '07:00', '07:30', '08:30'], array_column($cells, 'start'));
        $this->assertEquals(['07:00', '07:30', '08:30', '09:00'], array_column($cells, 'end'));
        $this->assertEquals(200000.0, $cells[0]['price']);
        $this->assertEquals(100000.0, $cells[1]['price']);
        $this->assertEquals(300000.0, $cells[2]['price']);
        $this->assertEquals(150000.0, $cells[3]['price']);
    }

    #[Test]
    public function fills_gap_between_frames_with_zero_price_placeholder(): void
    {
        // 2 khung cách nhau: 6:00-7:00 + 8:00-9:00
        // → khoảng 7:00-8:00 phải hiện ra như một ô không đặt được (is_open = false)
        $slots = collect([
            $this->makeSlot('06:00', '07:00', 150000),
            $this->makeSlot('08:00', '09:00', 150000),
        ]);

        $cells = CourtSlot::buildTimeCells($slots, self::DATE_MONDAY);

        $this->assertCount(3, $cells);
        $this->assertTrue($cells[0]['is_open']);
        $this->assertFalse($cells[1]['is_open']); // ô 7:00-8:00 trong khoảng trống
        $this->assertEquals('07:00', $cells[1]['start']);
        $this->assertEquals('08:00', $cells[1]['end']);
        $this->assertTrue($cells[2]['is_open']);
    }

    #[Test]
    public function uses_effective_price_for_peak_slots(): void
    {
        // Slot giờ vàng: is_peak = true, peak_price 500k → giá ô = 500k
        $slot = $this->makeSlot('18:00', '20:00', 200000, isPeak: true, peakPrice: 500000);

        $cells = CourtSlot::buildTimeCells(collect([$slot]), self::DATE_MONDAY);

        $this->assertEquals(500000.0, $cells[0]['price']);
        $this->assertEquals(500000.0, $cells[1]['price']);
    }

    #[Test]
    public function filters_slots_by_day_of_week(): void
    {
        // 2026-09-07 là thứ Hai (day_of_week = 1)
        $mondayOnly = $this->makeSlot('06:00', '07:00', 100000, dayOfWeek: 1);
        $weekendOnly = $this->makeSlot('09:00', '10:00', 100000, dayOfWeek: 6); // Thứ Bảy

        $cells = CourtSlot::buildTimeCells(collect([$mondayOnly, $weekendOnly]), self::DATE_MONDAY);

        $this->assertCount(1, $cells);
        $this->assertEquals('06:00', $cells[0]['start']);
    }

    #[Test]
    public function null_day_of_week_applies_to_every_day(): void
    {
        $everyDay = $this->makeSlot('06:00', '07:00', 100000, dayOfWeek: null);

        $mondayCells = CourtSlot::buildTimeCells(collect([$everyDay]), self::DATE_MONDAY);
        $sundayCells = CourtSlot::buildTimeCells(collect([$everyDay]), self::DATE_SUNDAY);

        $this->assertCount(1, $mondayCells);
        $this->assertCount(1, $sundayCells);
    }

    #[Test]
    public function returns_empty_for_no_matching_slots(): void
    {
        $cells = CourtSlot::buildTimeCells(collect([]), self::DATE_MONDAY);

        $this->assertSame([], $cells);
    }

    // --- Helpers ---

    private function makeSlot(
        string $start,
        string $end,
        float $price,
        ?int $dayOfWeek = 1, // mặc định thứ Hai để khớp DATE_MONDAY
        bool $isPeak = false,
        ?float $peakPrice = null,
    ): CourtSlot {
        $slot = new CourtSlot([
            'start_time' => $start,
            'end_time' => $end,
            'price' => $price,
            'day_of_week' => $dayOfWeek,
            'is_peak' => $isPeak,
            'peak_price' => $peakPrice,
        ]);

        return $slot;
    }
}
