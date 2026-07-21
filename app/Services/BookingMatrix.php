<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Models\Lead;
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
        $allTimes = array_values((array) setting('booking_time_slots', ['9:00 AM', '12:00 PM', '3:00 PM']));

        // Fetch all currently booked slots from today onward
        $bookedSlots = Lead::query()
            ->where('status', LeadStatus::Booked)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', Carbon::today())
            ->get()
            ->map(fn ($lead) => $lead->scheduled_at->format('Y-m-d g:i A'))
            ->toArray();

        $days = [];
        $cursor = Carbon::today();
        $added = 0;

        while ($added < $daysToOffer) {
            $cursor = $cursor->addDay();

            if ($cursor->isWeekend()) {
                continue;
            }

            $dateStr = $cursor->toDateString();

            // Filter times to exclude ones that match the booked slots
            $availableTimes = array_filter($allTimes, function ($time) use ($dateStr, $bookedSlots) {
                // Standardize the time format to ensure it matches 'g:i A' (e.g. 9:00 AM)
                $formattedTime = Carbon::parse($time)->format('g:i A');

                return ! in_array("{$dateStr} {$formattedTime}", $bookedSlots);
            });

            $days[] = [
                'date' => $dateStr,
                'label' => $cursor->format('D, M j'),
                'times' => array_values($availableTimes),
            ];

            $added++;
        }

        return $days;
    }
}
