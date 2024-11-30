<?php

namespace App\Http\Resources;

use App\Trait\CollectionPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use CollectionPagination;

    public static $wrap = "user";
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->when($this->resource->email, $this->resource->email),
            'email_verified_at' =>  $this->whenHas('email_verified_at', $this->email_verified_at),   //if field is selected
            // 'updated_at' => $this->when($request->user()->isAdmin(), $this->updated_at),
            // $this->mergeWhen($request->user()->canAny(['Update Password', 'View Password']), [
            //   'password' => $this->password,
            // ]),
            // 'roles' => $this->whenLoaded('roles'),  // if query having with()
            // 'roles' => RoleResource::collection($this->whenLoaded('roles')),  // if query having with()
            'user_url' => route('users.show', $this->id),
        ];
    }
}
