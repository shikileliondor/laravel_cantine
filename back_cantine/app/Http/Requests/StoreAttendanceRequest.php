<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'date' => ['nullable', 'date'],
            'heure_pointage' => ['nullable', 'date_format:H:i:s'],
            'type_repas' => ['sometimes', 'string', 'in:petit_dejeuner,dejeuner,gouter,diner'],
            'present' => ['sometimes', 'boolean'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
