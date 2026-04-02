<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CertificateRequest;
use App\Models\CertificateType;
use App\Models\MedicalRecord;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Reports overview page.
     */
    public function index(Request $request)
    {
        // ── Period handling ──────────────────────────────────────
        $mode = $request->get('mode', 'monthly');
        $now = Carbon::now();

        if ($mode === 'yearly') {
            $year = (int) $request->get('year', $now->year);
            $startDate = Carbon::create($year, 1, 1)->toDateString();
            $endDate = Carbon::create($year, 12, 31)->toDateString();
        } elseif ($mode === 'custom') {
            $startDate = $request->get('start_date', $now->copy()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', $now->copy()->endOfMonth()->toDateString());
        } else {
            // monthly (default)
            $month = (int) $request->get('month', $now->month);
            $year = (int) $request->get('year', $now->year);
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        }

        $selectedMonth = $request->get('month', $now->month);
        $selectedYear = $request->get('year', $now->year);

        // ── Appointment summary ─────────────────────────────────
        $appointments = Appointment::whereBetween('date', [$startDate, $endDate])->get();

        $certRequests = CertificateRequest::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59',
        ])->get();

        $certSummary = [
            'total'              => $certRequests->count(),
            'approved'           => $certRequests->where('status', 'approved')->count(),
            'pending'            => $certRequests->where('status', 'pending')->count(),
            'documents_verified' => $certRequests->where('status', 'documents_verified')->count(),
            'rejected'           => $certRequests->where('status', 'rejected')->count(),
        ];

        $uniqueStudents = $appointments->where('status', 'completed')
            ->pluck('student_id')
            ->merge($certRequests->pluck('student_id'))
            ->unique()
            ->count();

        $summary = [
            'total' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'no_show' => $appointments->where('status', 'no_show')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'approved' => $appointments->where('status', 'approved')->count(),
            'rejected' => $appointments->where('status', 'rejected')->count(),
            'unique_students' => $uniqueStudents,
        ];

        // ── By service ──────────────────────────────────────────
        $byService = Service::all()->map(function ($service) use ($startDate, $endDate) {
            $svcAppointments = Appointment::where('service_id', $service->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $total = $svcAppointments->count();
            $completed = $svcAppointments->where('status', 'completed')->count();

            return [
                'service' => $service->name,
                'color' => $service->color,
                'total' => $total,
                'completed' => $completed,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100) : 0,
            ];
        });

        $byCertType = CertificateType::all()->map(function ($certType) use ($startDate, $endDate) {
            $reqs = CertificateRequest::where('certificate_type_id', $certType->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();
            $total     = $reqs->count();
            $completed = $reqs->where('status', 'approved')->count();
            return [
                'service'         => $certType->name,
                'color'           => $certType->color,
                'total'           => $total,
                'completed'       => $completed,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100) : 0,
                'is_certificate'  => true,
            ];
        })->filter(fn ($item) => $item['total'] > 0)->values();

        $byService = $byService->concat($byCertType)->values();

        // ── By staff ────────────────────────────────────────────
        $byStaff = User::staff()->get()->map(function ($staff) use ($startDate, $endDate) {
            $staffAppointments = Appointment::where('staff_id', $staff->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            return [
                'staff' => $staff->name,
                'total' => $staffAppointments->count(),
                'completed' => $staffAppointments->where('status', 'completed')->count(),
            ];
        });

        // ── Daily trend ─────────────────────────────────────────
        $dailyTrend = collect();
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $certByDate = $certRequests->groupBy(
            fn ($r) => Carbon::parse($r->created_at)->toDateString()
        );

        while ($current->lte($end)) {
            $dateStr  = $current->toDateString();
            $dayAppts = $appointments->where('date', $dateStr);
            $dayCerts = $certByDate->get($dateStr, collect());
            $dailyTrend->push([
                'date'         => $current->format('M d'),
                'total'        => $dayAppts->count() + $dayCerts->count(),
                'completed'    => $dayAppts->where('status', 'completed')->count()
                               + $dayCerts->where('status', 'approved')->count(),
                'appointments' => $dayAppts->count(),
                'certificates' => $dayCerts->count(),
            ]);
            $current->addDay();
        }

        // ── Common cases ────────────────────────────────────────
        $completedAppointments = $appointments->where('status', 'completed');
        $completedAppointmentIds = $completedAppointments->pluck('id');

        // Top reasons — from appointment reason field (populated by presets)
        $topReasons = $completedAppointments
            ->filter(fn ($a) => !empty($a->reason))
            ->groupBy(fn ($a) => mb_strtolower(trim($a->reason)))
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(10);

        // Top diagnoses — from medical records of completed appointments
        $topDiagnoses = MedicalRecord::whereIn('appointment_id', $completedAppointmentIds)
            ->whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->select(DB::raw('LOWER(diagnosis) as diagnosis'), DB::raw('COUNT(*) as total'))
            ->groupBy('diagnosis')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Top cert request purposes
        $topCertReasons = $certRequests
            ->filter(fn ($r) => !empty($r->purpose_type))
            ->groupBy(function ($r) {
                $label = ($r->purpose_type === 'other' && !empty($r->purpose_text))
                    ? $r->purpose_text
                    : $r->purpose_type;
                return mb_strtolower(trim($label));
            })
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(10);

        // ── Student demographics (distinct students with completed appointments) ──
        $servedStudentIds = $appointments->where('status', 'completed')->pluck('student_id')
            ->merge($certRequests->pluck('student_id'))
            ->unique();
        $servedStudents = User::whereIn('id', $servedStudentIds)->get();

        $departmentLabels = [
            'coed' => 'College of Education',
            'cba' => 'College of Business Administration',
            'ceta' => 'College of Engineering, Technology & Architecture',
            'ccje' => 'College of Criminal Justice Education',
            'shs' => 'Senior High School',
        ];

        // By gender
        $byGender = $servedStudents->groupBy('sex')->map->count()->sortDesc();

        // By age brackets
        $byAge = $servedStudents->map(function ($student) {
            if (!$student->dob) {
                return 'Unknown';
            }
            $age = Carbon::parse($student->dob)->age;
            if ($age <= 17) return '15–17';
            if ($age <= 20) return '18–20';
            if ($age <= 23) return '21–23';
            return '24+';
        })->countBy()->sortKeys();

        // By department
        $byDepartment = $servedStudents->groupBy(function ($student) use ($departmentLabels) {
            $dept = strtolower($student->department ?? '');
            return $departmentLabels[$dept] ?? ucfirst($dept ?: 'Unknown');
        })->map->count()->sortDesc();

        // By year level
        $byYearLevel = $servedStudents->groupBy(function ($student) {
            return ucfirst(str_replace('-', ' ', $student->year_level ?? 'Unknown'));
        })->map->count()->sortDesc();

        // By program
        $byProgram = $servedStudents->groupBy(function ($student) {
            return strtoupper($student->program ?? 'Unknown');
        })->map->count()->sortDesc();

        $completionRate = ($summary['total'] + $certSummary['total']) > 0
            ? round((($summary['completed'] + $certSummary['approved']) / ($summary['total'] + $certSummary['total'])) * 100)
            : 0;

        return view('admin.reports', compact(
            'summary',
            'certSummary',
            'completionRate',
            'byService',
            'byStaff',
            'dailyTrend',
            'startDate',
            'endDate',
            'mode',
            'selectedMonth',
            'selectedYear',
            'topReasons',
            'topDiagnoses',
            'topCertReasons',
            'byGender',
            'byAge',
            'byDepartment',
            'byYearLevel',
            'byProgram'
        ));
    }
}
