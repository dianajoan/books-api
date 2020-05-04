<?php

declare (strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
// use Illuminate\Http\Resources\Json\ResourceCollection;
use App\BookAuthor;
use App\Author;
use App\BookReview;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $authz = BookAuthor::where('book_id', $this->id)->get();
        $revvz = BookReview::where('book_id', $this->id)->get();
        $total_revs = BookReview::all();

        $persons = array();

        foreach ($authz as $user) {
            array_push($persons, [
                'id'    => Author::where('id',$user->author_id)->first()->id,
                'name'  => Author::where('id',$user->author_id)->first()->name,
                'surname' => Author::where('id',$user->author_id)->first()->surname
            ]);
        }

        $counter = count($total_revs);
        $total = 0;

        foreach ($revvz as $rev) {
            $total = $total + $rev->review;
        }

        $revs = [
            'avg'    => count($revvz) > 0 ? round($total/count($revvz),0) : 0,
            'count'  => count($revvz)
            ];
        return [
            // @TODO implement
            'id' => $this->id,
            'isbn'      => $this->isbn,
            'title'     => $this->title,
            'description' => $this->description,
            'authors' => $persons,
            'review'=> $revs
        ];
    }
}
