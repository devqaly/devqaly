<?php

namespace App\Http\Resources\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CompanyMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return collect([
            'id' => $this->id,
            'createdAt' => $this->created_at,
            'invitedBy' => new UserResource($this->whenLoaded('invitedBy')),
            'member' => new UserResource($this->whenLoaded('member')),
        ])
            // When a user is invited we create a RegisterToken for him to be able to register.
            // When the user accepted the invitation, we convert the RegisterToken into a User.
            // In case that there are no User associate with this CompanyMember yet,
            // we will include the email that was used to invite the user.
            ->when($this->resource->relationLoaded('registerToken') && $this->resource->registerToken, function (Collection $collection) {
                $collection->put('registerToken', [
                    'email' => $this->resource->registerToken->email,
                    'id' => $this->resource->registerToken->id,
                ]);
            })
            ->toArray();
    }
}
