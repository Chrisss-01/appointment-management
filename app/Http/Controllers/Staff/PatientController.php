<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * List all student patients.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $patients = User::students()
            ->active()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('student_id', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount(['studentAppointments as total_visits' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->orderBy('name')
            ->paginate(20);

        return view('staff.patients', compact('patients', 'search'));
    }

    /**
     * Show a specific patient's records.
     */
    public function show(User $patient)
    {
        if (!$patient->isStudent()) {
            abort(404);
        }

        $medicalRecords = MedicalRecord::where('student_id', $patient->id)
            ->with(['staff', 'appointment.service'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $appointments = $patient->studentAppointments()
            ->with('service')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        return view('staff.patient-detail', compact('patient', 'medicalRecords', 'appointments'));
    }

    /**
     * Store a medical record for a patient.
     */
    public function storeMedicalRecord(StoreMedicalRecordRequest $request)
    {
        $validated = $request->validated();
        $validated['staff_id'] = $request->user()->id;

        if (empty($validated['service_name'])) {
            $validated['service_name'] = ucfirst($validated['record_type']) . ' Record';
        }

        $record = MedicalRecord::create($validated);

        return back()->with('success', 'Medical record saved successfully.');
    }
}
