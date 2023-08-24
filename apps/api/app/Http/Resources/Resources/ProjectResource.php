<?php

namespace App\Http\Resources\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'projectKey' => $this->project_key,
            'securityToken' => $this->security_token,
            'createdAt' => $this->created_at,
        ];
    }
}
