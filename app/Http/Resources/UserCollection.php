<?php

namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

// class UserCollection extends ResourceCollection
class UserCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'  => $this->name,
            'email' => $this->email,
            'verify' => $this->email_verified_at ? 'verified' : 'not verified',
            'type'  => $this->is_admin ? 'admin' : 'normal user',
            'link'  => route('users.show', $this->id)
        ];
        // return parent::toArray($request);
    }
}
