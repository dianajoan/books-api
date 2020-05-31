<?php

namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Book;
use App\User;

// class BookReviewCollection extends ResourceCollection
class BookReviewCollection extends JsonResource
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
            'id'        => $this->id,
            'review'    => $this->review,
            'comment'   => $this->comment,
            'user'      => array(
                'id'    => $this->user_id ? User::where('id',$this->user_id)->first()->id : '',
                'name'  => $this->user_id ? User::where('id',$this->user_id)->first()->name : ''
            )
        ];
        // return parent::toArray($request);
    }
}
