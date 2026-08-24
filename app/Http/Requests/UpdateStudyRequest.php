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
            'engine_visibility' => 'sometimes|required|in:everyone,owner',
            'export_visibility' => 'sometimes|required|in:everyone,owner',
            'category' => 'sometimes|required|string|in:general,opening_repertoire,middlegame,endgame',
            'orientation' => 'sometimes|required|string|in:white,black',
            'preview_fen' => 'sometimes|nullable|string',
            'preview_last_move' => 'sometimes|nullable|string',
        ];
    }
}
