<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uid' => (string) $this->id,
            'username' => $this->name,
            'displayName' => $this->name,
        ];
    }
}
