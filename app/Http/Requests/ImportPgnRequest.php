<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportPgnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized in controller via Policy
    }

    public function rules(): array
    {
        return [
            'pgn' => 'required|string',
        ];
    }
}
