<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;

class QueueService
{
    /**
     * Get the current queue for a specific date and optional service.
     */
    public function getTodayQueue(?int $serviceId = null): array
    {
        $query = Appointment::with(['student', 'service', 'nurse'])
            ->where('date', Carbon::today())
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('queue_number');

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        $appointments = $query->get();

        return [
            'pending' => $appointments->where('status', 'pending')->values(),
            'approved' => $appointments->where('status', 'approved')->values(),
            'total' => $appointments->count(),
            'next_queue_number' => $appointments->max('queue_number') + 1,
        ];
    }

    /**
     * Get queue statistics for a date range.
     */
    public function getQueueStats(string $startDate, string $endDate): array
    {
        $appointments = Appointment::whereBetween('date', [$startDate, $endDate])->get();

        return [
            'total' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'no_show' => $appointments->where('status', 'no_show')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'approved' => $appointments->where('status', 'approved')->count(),
        ];
    }
}
