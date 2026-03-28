<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Reports overview page.
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Appointment summary
        $appointments = Appointment::whereBetween('date', [$startDate, $endDate])->get();

        $summary = [
            'total' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'no_show' => $appointments->where('status', 'no_show')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'approved' => $appointments->where('status', 'approved')->count(),
            'rejected' => $appointments->where('status', 'rejected')->count(),
        ];

        // By service
        $byService = Service::all()->map(function ($service) use ($startDate, $endDate) {
            $appointments = Appointment::where('service_id', $service->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            return [
                'service' => $service->name,
                'color' => $service->color,
                'total' => $appointments->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
            ];
        });

        // By staff
        $byStaff = User::staff()->get()->map(function ($staff) use ($startDate, $endDate) {
            $appointments = Appointment::where('staff_id', $staff->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            return [
                'staff' => $staff->name,
                'total' => $appointments->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
            ];
        });

        // Daily trend
        $dailyTrend = collect();
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current->lte($end)) {
            $dayAppointments = $appointments->where('date', $current->toDateString());
            $dailyTrend->push([
                'date' => $current->format('M d'),
                'total' => $dayAppointments->count(),
                'completed' => $dayAppointments->where('status', 'completed')->count(),
            ]);
            $current->addDay();
        }

        return view('admin.reports', compact(
            'summary',
            'byService',
            'byStaff',
            'dailyTrend',
            'startDate',
            'endDate'
        ));
    }
}
