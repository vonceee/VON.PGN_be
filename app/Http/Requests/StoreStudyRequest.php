<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Controller handles authorization via Policy if needed, but creation is public
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'visibility' => 'required|in:public,private,unlisted',
            'engine_visibility' => 'sometimes|required|in:everyone,owner',
        ];
    }
}
