<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized in controller via Policy
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'visibility' => 'sometimes|required|in:public,private,unlisted',
        ];
    }
}
