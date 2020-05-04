<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Book;
use App\Author;

class BookAuthor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // adding missing fields for model
    protected $fillable = [
        'book_id',
        'author_id',
    ];

    /**
     * The string variable is for the table.
     *
     * @var array
     */
    protected $table = 'book_author';

    // removing timestamps
    public $timestamps = false;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
