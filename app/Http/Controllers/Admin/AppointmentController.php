<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * View all appointments across the clinic.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $appointments = Appointment::with(['student', 'staff', 'service'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->paginate(20);

        return view('admin.appointments', compact('appointments', 'status'));
    }
}
