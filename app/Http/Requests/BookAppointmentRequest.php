<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isStudent();
    }

    public function rules(): array
    {
        return [
            'generated_slot_id' => [
                'required',
                'exists:generated_slots,id',
                function ($attribute, $value, $fail) {
                    $slot = \App\Models\GeneratedSlot::find($value);
                    if (!$slot) return;

                    if ($slot->status !== 'available') {
                        $fail('This time slot is no longer available.');
                    }

                    if (\Carbon\Carbon::parse($slot->date->format('Y-m-d') . ' ' . $slot->start_time)->isPast()) {
                        $fail('Cannot book an appointment in the past.');
                    }

                    $existing = \App\Models\Appointment::where('student_id', $this->user()->id)
                        ->where('date', $slot->date)
                        ->where('service_id', $slot->service_id)
                        ->whereNotIn('status', ['cancelled', 'rejected', 'no_show'])
                        ->exists();

                    if ($existing) {
                        $fail('You already have an appointment for this service on this date.');
                    }
                },
            ],
            'reason' => 'nullable|string|max:500',
            'additional_comments' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'generated_slot_id.required' => 'Please select a time slot.',
            'generated_slot_id.exists' => 'The selected time slot is invalid.',
        ];
    }
}
