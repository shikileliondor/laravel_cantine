<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['sometimes', 'required', 'string', 'max:255'],
            'prenom' => ['sometimes', 'required', 'string', 'max:255'],
            'classe' => ['sometimes', 'required', 'string', 'max:100'],
            'date_naissance' => ['nullable', 'date'],
            'nom_tuteur' => ['sometimes', 'required', 'string', 'max:255'],
            'telephone_tuteur' => ['sometimes', 'required', 'string', 'max:30'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'actif' => ['sometimes', 'boolean'],
        ];
    }
}
