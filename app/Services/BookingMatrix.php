<?php

namespace App\Services;

use Illuminate\Support\Carbon;

/**
 * Generates the localized site-visit booking grid (next N business days × time slots).
 */
class BookingMatrix
{
    /**
     * @return array<int, array{date: string, label: string, times: array<int, string>}>
     */
    public function slots(): array
    {
        $daysToOffer = max(1, (int) setting('booking_days_offered', 5));
        $times = array_values((array) setting('booking_time_slots', ['9:00 AM', '12:00 PM', '3:00 PM']));

        $days = [];
        $cursor = Carbon::today();
        $added = 0;

        while ($added < $daysToOffer) {
            $cursor = $cursor->addDay();

            if ($cursor->isWeekend()) {
                continue;
            }

            $days[] = [
                'date' => $cursor->toDateString(),
                'label' => $cursor->format('D, M j'),
                'times' => $times,
            ];

            $added++;
        }

        return $days;
    }
}
