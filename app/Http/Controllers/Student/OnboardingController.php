<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function showDepartment()
    {
        return view('onboarding.department');
    }

    public function showProgram(Request $request)
    {
        $department = $request->query('department');

        $programsByDepartment = [
            'coed' => [
                ['code' => 'BEED', 'name' => 'Bachelor of Elementary Education', 'icon' => 'school'],
                ['code' => 'BSED FIL', 'name' => 'Bachelor of Secondary Education Major in Filipino', 'icon' => 'translate'],
                ['code' => 'BSED ENG', 'name' => 'Bachelor of Secondary Education Major in English', 'icon' => 'menu_book'],
                ['code' => 'BSED SOST', 'name' => 'Bachelor of Secondary Education Major in Social Studies', 'icon' => 'public'],
            ],
            'cba' => [
                ['code' => 'BS TM', 'name' => 'Bachelor of Science in Tourism Management', 'icon' => 'travel_explore'],
                ['code' => 'BS HM', 'name' => 'Bachelor of Science in Hospitality Management', 'icon' => 'hotel'],
                ['code' => 'BSBA MM', 'name' => 'Bachelor of Science in Business Administration Major in Marketing Management', 'icon' => 'campaign'],
            ],
            'ccje' => [
                ['code' => 'BS CRIM', 'name' => 'Bachelor of Science in Criminology', 'icon' => 'gavel'],
            ],
            'ceta' => [
                ['code' => 'BS CS', 'name' => 'Bachelor of Science in Computer Science', 'icon' => 'memory'],
                ['code' => 'BS EE', 'name' => 'Bachelor of Science in Electrical Engineering', 'icon' => 'bolt'],
                ['code' => 'BS ME', 'name' => 'Bachelor of Science in Mechanical Engineering', 'icon' => 'precision_manufacturing'],
            ],
            'shs' => [
                ['code' => 'HUMSS', 'name' => 'Humanities and Social Sciences', 'icon' => 'menu_book'],
                ['code' => 'STEM', 'name' => 'Science, Technology, Engineering, Mathematics', 'icon' => 'science'],
                ['code' => 'ABM', 'name' => 'Accountancy, Business, and Management', 'icon' => 'bar_chart'],
                ['code' => 'GAS', 'name' => 'General Academic Strand', 'icon' => 'school'],
                ['code' => 'TVL', 'name' => 'Technical-Vocational-Livelihood', 'icon' => 'build'],
            ],
        ];

        if (!$department || !array_key_exists($department, $programsByDepartment)) {
            return redirect()->route('onboarding.department');
        }

        return view('onboarding.program', [
            'department' => $department,
            'programs' => $programsByDepartment[$department],
        ]);
    }

    public function showYearLevel(Request $request)
    {
        $department = $request->query('department');
        $program = $request->query('program');

        if (!$department || !$program) {
            return redirect()->route('onboarding.department');
        }

        $yearLevels = $department === 'shs'
            ? [
                ['value' => 'grade-11', 'label' => 'Grade 11'],
                ['value' => 'grade-12', 'label' => 'Grade 12'],
            ]
            : [
                ['value' => '1st-year', 'label' => '1st Year'],
                ['value' => '2nd-year', 'label' => '2nd Year'],
                ['value' => '3rd-year', 'label' => '3rd Year'],
                ['value' => '4th-year', 'label' => '4th Year'],
            ];

        return view('onboarding.year-level', [
            'department' => $department,
            'program' => $program,
            'yearLevels' => $yearLevels,
        ]);
    }

    public function showStudentId(Request $request)
    {
        $department = $request->query('department');
        $program = $request->query('program');
        $yearLevel = $request->query('year_level');

        if (!$department || !$program || !$yearLevel) {
            return redirect()->route('onboarding.department');
        }

        return view('onboarding.student-id', [
            'department' => $department,
            'program' => $program,
            'yearLevel' => $yearLevel,
        ]);
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'department' => ['required', 'string'],
            'program' => ['required', 'string'],
            'year_level' => ['required', 'string'],
            'student_id' => [
                'required', 
                'string', 
                Rule::unique('users', 'student_id')->ignore($request->user()->id)
            ],
        ]);

        $request->user()->update([
            'department' => $validated['department'],
            'program' => $validated['program'],
            'year_level' => $validated['year_level'],
            'student_id' => $validated['student_id'],
        ]);

        return redirect()->route('onboarding.success');
    }

    public function showSuccess()
    {
        $onboardingIntent = session()->pull('onboarding_intent');
        
        // Determine the redirect URL and message
        if ($onboardingIntent && str_contains($onboardingIntent, 'student/services')) {
            $redirectUrl = $onboardingIntent;
            $successMessage = "Your student profile is complete. Taking you to the booking page...";
        } else {
            $redirectUrl = route('student.dashboard');
            $successMessage = "Your student profile is complete. Taking you to your home page...";
        }

        return view('onboarding.success', [
            'redirectUrl' => $redirectUrl,
            'successMessage' => $successMessage
        ]);
    }
}
