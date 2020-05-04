<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// adding missing model imports
use App\User;
use App\Book;

class BookReview extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // adding missing fields for model
    protected $fillable = [
        'book_id',
        'user_id',
        'comment',
        'review',
    ];
    
    /**
     * The string variable is for the table.
     *
     * @var array
     */
    protected $table = 'book_reviews';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
