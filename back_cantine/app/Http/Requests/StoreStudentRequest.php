<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'classe' => ['required', 'string', 'max:100'],
            'date_naissance' => ['nullable', 'date'],
            'nom_tuteur' => ['required', 'string', 'max:255'],
            'telephone_tuteur' => ['required', 'string', 'max:30'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'actif' => ['sometimes', 'boolean'],
        ];
    }
}
