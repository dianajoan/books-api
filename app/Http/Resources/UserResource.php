<?php

declare (strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // adding a few info for individual usage
            'email' => $this->email,
            'type'  => $this->is_admin ? 'admin' : 'user',
            'verified' => $this->email_verified_at ? 'verified' : 'not verified',
            'joined'=> $this->created_at,
        ];
    }
}
