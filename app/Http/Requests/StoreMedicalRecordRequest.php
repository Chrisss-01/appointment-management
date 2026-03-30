<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'record_type' => 'required|in:consultation,dental,general',
            'service_name' => 'nullable|string|max:255',
            'chief_complaint' => 'nullable|string|max:1000',
            'diagnosis' => 'nullable|string|max:1000',
            'treatment' => 'nullable|string|max:1000',
            'prescription' => 'nullable|string|max:1000',
            'vital_signs' => 'nullable|array',
            'vital_signs.blood_pressure' => 'nullable|string|max:20',
            'vital_signs.temperature' => 'nullable|numeric|min:30|max:45',
            'vital_signs.heart_rate' => 'nullable|integer|min:30|max:250',
            'vital_signs.weight' => 'nullable|numeric|min:1|max:500',
            'vital_signs.height' => 'nullable|numeric|min:30|max:300',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
