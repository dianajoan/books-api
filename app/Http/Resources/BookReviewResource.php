<?php

declare (strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

//importing the books and users models
use App\Book;
use App\User;

class BookReviewResource extends JsonResource
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
            // @TODO implement
            'id'        => $this->id,
            'review'    => $this->review,
            'comment'   => $this->comment,
            'user'      => array(
                'id'    => $this->user_id ? User::where('id',$this->user_id)->first()->id : '',
                'name'  => $this->user_id ? User::where('id',$this->user_id)->first()->name : ''
            )
        ];
    }
}
