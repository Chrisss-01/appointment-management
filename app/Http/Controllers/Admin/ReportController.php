<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CertificateRequest;
use App\Models\CertificateType;
use App\Models\MedicalRecord;
use App\Models\Service;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Reports overview page.
     */
    public function index(Request $request)
    {
        $data = $this->getReportData($request);

        return view('admin.reports', $data);
    }

    /**
     * Export analytics report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);

        // Build human-readable filters summary
        $filtersApplied = [];

        if ($data['mode'] === 'monthly') {
            $filtersApplied[] = 'Month: ' . Carbon::create(null, $data['selectedMonth'])->format('F') . ' ' . $data['selectedYear'];
        } elseif ($data['mode'] === 'yearly') {
            $filtersApplied[] = 'Year: ' . $data['selectedYear'];
        } else {
            $filtersApplied[] = 'Period: ' . Carbon::parse($data['startDate'])->format('M d, Y') . ' – ' . Carbon::parse($data['endDate'])->format('M d, Y');
        }

        if ($data['studentFilter'] !== 'all' && $data['studentFilterValue'] !== '') {
            $filterLabel = match ($data['studentFilter']) {
                'department' => 'Department: ' . ($data['departmentLabels'][strtolower($data['studentFilterValue'])] ?? ucfirst($data['studentFilterValue'])),
                'program'    => 'Program: ' . strtoupper($data['studentFilterValue']),
                'year_level' => 'Year Level: ' . ucfirst(str_replace('-', ' ', $data['studentFilterValue'])),
                default      => '',
            };
            if ($filterLabel) {
                $filtersApplied[] = $filterLabel;
            }
        }

        $data['filtersApplied'] = $filtersApplied;
        $data['generatedAt'] = Carbon::now()->format('F d, Y – h:i A');

        $pdf = Pdf::loadView('admin.reports-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        // Build filename
        $filenameParts = ['clinic-report'];
        if ($data['mode'] === 'monthly') {
            $filenameParts[] = $data['selectedYear'] . '-' . str_pad($data['selectedMonth'], 2, '0', STR_PAD_LEFT);
        } elseif ($data['mode'] === 'yearly') {
            $filenameParts[] = $data['selectedYear'];
        } else {
            $filenameParts[] = Carbon::parse($data['startDate'])->format('Y-m-d') . '_to_' . Carbon::parse($data['endDate'])->format('Y-m-d');
        }

        if ($data['studentFilter'] !== 'all' && $data['studentFilterValue'] !== '') {
            $filenameParts[] = Str::slug($data['studentFilterValue']);
        }

        $filename = implode('-', $filenameParts) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Gather all report/analytics data based on request filters.
     */
    private function getReportData(Request $request): array
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

        // ── Student filter ──────────────────────────────────────
        $studentFilter = $request->get('student_filter', 'all');
        $studentFilterValue = $request->get('student_filter_value', '');

        $departmentLabels = [
            'coed' => 'College of Education',
            'cba'  => 'College of Business Administration',
            'ceta' => 'College of Engineering, Technology & Architecture',
            'ccje' => 'College of Criminal Justice Education',
            'shs'  => 'Senior High School',
        ];

        $departments = collect(array_keys($departmentLabels));

        $programsByDepartment = [
            'coed' => ['BEED', 'BSED FIL', 'BSED ENG', 'BSED SOST'],
            'cba'  => ['BS TM', 'BS HM', 'BSBA MM'],
            'ccje' => ['BS CRIM'],
            'ceta' => ['BS CS', 'BS EE', 'BS ME'],
            'shs'  => ['HUMSS', 'STEM', 'ABM', 'GAS', 'TVL'],
        ];
        $programs = collect($programsByDepartment)->flatten()->sort()->values();

        $yearLevels = collect([
            ['value' => '1st-year', 'label' => '1st Year'],
            ['value' => '2nd-year', 'label' => '2nd Year'],
            ['value' => '3rd-year', 'label' => '3rd Year'],
            ['value' => '4th-year', 'label' => '4th Year'],
            ['value' => 'grade-11', 'label' => 'Grade 11'],
            ['value' => 'grade-12', 'label' => 'Grade 12'],
        ]);

        $filteredStudentIds = null;
        if ($studentFilter !== 'all' && $studentFilterValue !== '') {
            $column = match ($studentFilter) {
                'department' => 'department',
                'program'    => 'program',
                'year_level' => 'year_level',
                default      => null,
            };
            if ($column) {
                $filteredStudentIds = User::students()->where($column, $studentFilterValue)->pluck('id');
            }
        }

        // ── Appointment summary ─────────────────────────────────
        $appointments = Appointment::whereBetween('date', [$startDate, $endDate])->get();

        $certRequests = CertificateRequest::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59',
        ])->get();

        // Apply student filter to collections
        if ($filteredStudentIds !== null) {
            $appointments = $appointments->whereIn('student_id', $filteredStudentIds)->values();
            $certRequests = $certRequests->whereIn('student_id', $filteredStudentIds)->values();
        }

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
        $byService = Service::all()->map(function ($service) use ($appointments) {
            $svcAppointments = $appointments->where('service_id', $service->id);

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

        $byCertType = CertificateType::all()->map(function ($certType) use ($certRequests) {
            $reqs = $certRequests->where('certificate_type_id', $certType->id);
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
        $byStaff = User::staff()->get()->map(function ($staff) use ($appointments) {
            $staffAppointments = $appointments->where('staff_id', $staff->id);

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
        $topReasons = $this->topFiveWithOther(
            $completedAppointments
                ->filter(fn ($a) => !empty($a->reason))
                ->groupBy(fn ($a) => mb_strtolower(trim($a->reason)))
                ->map(fn ($group) => $group->count())
                ->sortDesc()
        );

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
        $topCertReasons = $this->topFiveWithOther(
            $certRequests
                ->filter(fn ($r) => !empty($r->purpose_type))
                ->groupBy(function ($r) {
                    $label = ($r->purpose_type === 'other' && !empty($r->purpose_text))
                        ? $r->purpose_text
                        : $r->purpose_type;
                    return mb_strtolower(trim($label));
                })
                ->map(fn ($group) => $group->count())
                ->sortDesc()
        );

        // ── Student demographics (distinct students with completed appointments) ──
        $servedStudentIds = $appointments->where('status', 'completed')->pluck('student_id')
            ->merge($certRequests->pluck('student_id'))
            ->unique();
        $servedStudents = User::whereIn('id', $servedStudentIds)->get();

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
        $byProgram = $this->topFiveWithOther(
            $servedStudents->groupBy(function ($student) {
                return strtoupper($student->program ?? 'Unknown');
            })->map->count()->sortDesc()
        );

        $completionRate = ($summary['total'] + $certSummary['total']) > 0
            ? round((($summary['completed'] + $certSummary['approved']) / ($summary['total'] + $certSummary['total'])) * 100)
            : 0;

        return compact(
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
            'byProgram',
            'studentFilter',
            'studentFilterValue',
            'departments',
            'programs',
            'yearLevels',
            'departmentLabels',
            'programsByDepartment'
        );
    }

    private function topFiveWithOther($data)
    {
        if ($data->count() <= 5) {
            return $data;
        }

        $top = $data->take(5);
        $otherSum = $data->slice(5)->sum();

        if ($otherSum > 0) {
            $top->put('Other', $otherSum);
        }

        return $top;
    }
}
