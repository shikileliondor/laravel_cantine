<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pin_code' => ['required', 'digits:4'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
