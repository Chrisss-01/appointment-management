<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Admin analytics dashboard.
     */
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // General stats
        $totalStudents = User::students()->count();
        $totalStaff = User::staff()->count();
        $totalServices = Service::active()->count();

        // Appointment stats
        $todayAppointments = Appointment::where('date', $today)->count();
        $monthlyAppointments = Appointment::where('date', '>=', $thisMonth)->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $completedToday = Appointment::where('date', $today)->where('status', 'completed')->count();

        // Certificate stats
        $pendingCertificates = Certificate::where('status', 'pending')->count();

        // Weekly appointment trends (last 7 days)
        $weeklyTrend = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date' => $date->format('M d'),
                'count' => Appointment::where('date', $date)->count(),
                'completed' => Appointment::where('date', $date)->where('status', 'completed')->count(),
            ];
        });

        // Service distribution
        $serviceDistribution = Service::withCount(['appointments' => function ($q) use ($thisMonth) {
            $q->where('date', '>=', $thisMonth);
        }])->get()->map(fn($s) => [
            'name' => $s->name,
            'count' => $s->appointments_count,
            'color' => $s->color,
        ]);

        // Dynamically add Certificate Types to the distribution
        $certificateTypes = \App\Models\CertificateType::active()->withCount(['certificateRequests' => function ($q) use ($thisMonth) {
            $q->where('created_at', '>=', $thisMonth);
        }])->get();

        foreach ($certificateTypes as $type) {
            $serviceDistribution->push([
                'name' => $type->name . ' Request',
                'count' => $type->certificate_requests_count,
                'color' => $type->color,
            ]);
        }

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalStaff',
            'totalServices',
            'todayAppointments',
            'monthlyAppointments',
            'pendingAppointments',
            'completedToday',
            'pendingCertificates',
            'weeklyTrend',
            'serviceDistribution'
        ));
    }
}
