<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'montant' => ['required', 'numeric', 'min:0'],
            'date_paiement' => ['required', 'date'],
            'periode_debut' => ['nullable', 'date'],
            'periode_fin' => ['nullable', 'date', 'after_or_equal:periode_debut'],
            'mode_paiement' => ['sometimes', 'string', 'in:especes,cheque,virement,autre'],
            'reference' => ['nullable', 'string', 'max:255'],
            'observation' => ['nullable', 'string'],
        ];
    }
}
