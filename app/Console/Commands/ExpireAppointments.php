<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ExpireAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire pending appointments whose time has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // Find appointments that are pending and in the past
        $expiredAppointments = Appointment::pending()
            ->where(function ($query) use ($today, $currentTime) {
                // If the date is strictly before today
                $query->where('date', '<', $today)
                      // Or if it's today, but the end_time has passed
                      ->orWhere(function ($q) use ($today, $currentTime) {
                          $q->where('date', '=', $today)
                            ->where('end_time', '<', $currentTime);
                      });
            })
            ->get();

        $count = $expiredAppointments->count();

        foreach ($expiredAppointments as $appointment) {
            $appointment->expire('Not approved by clinic');

            // Notify the student
            Notification::send(
                $appointment->student_id,
                'appointment_expired',
                'Appointment Expired',
                "Your appointment for {$appointment->service->name} on {$appointment->date->format('M d, Y')} has expired because it wasn't approved by the clinic in time.",
                ['appointment_id' => $appointment->id]
            );
        }

        $this->info("Successfully expired {$count} pending appointment(s) and notified students.");
    }
}
