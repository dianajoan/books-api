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
            'book_id'   => $this->book_id ? Book::where('id',$this->book_id)->first()->title : 'unknown book',
            'user_id'   => $this->user_id ? User::where('id',$this->user_id)->first()->name : 'unknown book',
            'review'    => $this->review,
            'comment'   => $this->comment
        ];
        // return parent::toArray($request);
    }
}
