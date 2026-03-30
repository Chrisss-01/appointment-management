<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('manage-availability');
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'nullable|integer|min:5|max:60',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Please select a service type first.',
            'date.after_or_equal' => 'Cannot create availability for past dates.',
            'end_time.after' => 'End time must be after start time.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $date = $this->input('date');
            $startTime = $this->input('start_time');

            if ($date && $startTime && \Carbon\Carbon::parse($date)->isToday()) {
                $now = now()->format('H:i');
                if ($startTime <= $now) {
                    $validator->errors()->add('start_time', 'Start time must be after the current time for today.');
                }
            }
        });
    }
}
