<?php

//declare (strict_types=1);

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookCollection;
use App\Http\Requests\PostBookRequest;

use App\BookReview;
use App\Http\Resources\BookReviewResource;
use App\Http\Resources\BookReviewCollection;
use App\Http\Requests\PostBookReviewRequest;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as PublicRequest;
use App\BookAuthor;

use App\Author;
use App\User;

use Illuminate\Http\Request;

class BooksController extends Controller
{
    /**
     * Display the constructor of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth.admin')->except('getCollection','showBook', 'bookReview', 'showBookReview', 'updateBook', 'updateBookReview', 'destroy', 'deatroyBookReview');
    }

    /**
     * Getting the collection for all books stored
     * A total of all books
     * @return \Illuminate\Http\Response
     */
    public function getCollection(PublicRequest $request)
    {
        // @TODO implement
        // check if there is request data to sort columns
        if ( $request->sortColumn ) {
            if ( $request->sortColumn === 'title' ) {

                $books = Book::all();

                $request->sortDirection ? ( $order = $request->sortDirection ) : ( $order = 'asc' );

                if (sizeof($books) > 0) {
                    return BookCollection::collection(Book::orderBy('title', $order)->paginate(15));
                }

            } elseif ( $request->sortColumn === 'avg_review') {

                $bookRevsTotal = array();
                $books = Book::all();

                // getting array with book id and average review number per bk id
                foreach ($books as $bk) {
                    // getting all reviews per single book
                    $revvz = BookReview::where('book_id', $bk->id)->get();
                    
                    // initializing total of revs per bk as an interger
                    $total = 0;

                    // summing up all reviews of the bk
                    foreach ($revvz as $rev) {
                        $total = $total + $rev->review;
                    }

                    // getting an average of all reviews
                    $revavg = count($revvz) > 0 ? round($total/count($revvz),0) : 0;

                    // adding values per book with book id and average reviews to empty array
                    $bookRevsTotal[] = array(
                        'book_id'   => $bk->id,
                        'avgrevs'   => $revavg
                    );

                }

                // looping through book id with avg revs to single sort avg revs colum
                foreach ($bookRevsTotal as $key => $row) {
                    $avgrevs[$key] = $row['avgrevs'];
                }

                // getting an array from above with avg revs only
                $avgrevs = array_column($bookRevsTotal, 'avgrevs'); 

                // sorting the above array to ascending or decending given the request
                if ($request->sortDirection === 'DESC') {
                    array_multisort($avgrevs, SORT_DESC, $bookRevsTotal);
                } else {
                    array_multisort($avgrevs, SORT_ASC, $bookRevsTotal);
                }

                // now the array is sorted with book ids and avg revs
                // initialize an empty array to return content of $bookRevsTotal
                $finalResponse = array();

                foreach ($bookRevsTotal as $bookAndRev) {
                    $finalResponse[] = $this->showBook($bookAndRev['book_id']);
                }

                return response()->json(['data' => $finalResponse], JsonResponse::HTTP_OK);
            }

            return response()->json([
                'error' => 'Sorry, no books found under that author category!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ( $request->authors ) {
            $bookAuthorsTotal = array();
            $books = Book::all();

            $bookAuthrArr = array();

            // getting array with book id and authors number per bk id
            foreach ($books as $bk) {
                // 
                $bkAuth = BookAuthor::where('book_id', $bk->id)->get();

                foreach ($bkAuth as $value) {
                    array_push($bookAuthrArr, $value);
                }
            }

            $inputAuthr = explode(',', trim($request->authors));

            $filteredArr = array();

            foreach ($bookAuthrArr as $value) {
                foreach ($inputAuthr as $inputValue) {
                    if ($value['author_id'] === $inputValue ) {
                        $filteredArr[] = array('book_id' => $value['book_id']);
                    }
                }
            }

            $filteredAr = array_unique($filteredArr, 0);

            $finalResponse = array();

            foreach ($filteredAr as $bookAndAuthr) {
                $finalResponse[] = $this->showBook($bookAndAuthr['book_id']);
            }

            return response()->json(['data' => $finalResponse], JsonResponse::HTTP_OK);
        }

        // if no book found
        if (sizeof(Book::all()) < 1) {
            return response()->json([
                'error' => 'No books found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return BookCollection::collection(Book::paginate(15));   
    }

    public function post(PostBookRequest $request)
    {
        // @TODO implement
        foreach ($request->all() as $key => $value) {
            if ($key != 'isbn' && $key != 'title' && $key != 'description' && $key != 'authors' && $key != 'api_token') {
                return response()->json([
                    'error'  => 'No field found with name ' . $key
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        foreach ($request->authors as $value) {
            $get_user = Author::where('id',$value)->first();
            if (!$get_user) {
                return response()->json([
                    'errors'  => array('authors.0' => array('No author found with id ' . $value)
                )], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $book = new Book();
        $book->isbn     = $request->isbn;
        $book->title    = $request->title;
        $book->description    = $request->description;
        $book->save();

        $current_book = Book::where('title',$request->title)->first();
        foreach ($request->authors as $value) {
            $new_item = new BookAuthor();
            $new_item->book_id = $current_book->id;
            $new_item->author_id = $value;
            $new_item->save();
        }

        return response()->json([
            'data'  => new BookResource($book)
        ], JsonResponse::HTTP_CREATED);
    }

    // part two
    public function postReview(int $bookId, PostBookReviewRequest $request)
    {
        // @TODO implement

        $book = Book::find($bookId);

        if (!$book) {
            return response()->json([
                'error' => 'Book not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $req_user = User::find($request->user_id);

        if (!$req_user) {
            $request->user_id = 1;
        }

        if (!$request->review) {
            return response()->json([
                'error' => 'Please enter a valid review!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $book_rev = new BookReview();
        $book_rev->book_id  = $bookId;
        $book_rev->user_id  = $request->user_id;
        $book_rev->review   = $request->review;
        $book_rev->comment  = $request->comment;
        $book_rev->save();

        return response()->json([
            'data'  => new BookReviewResource($book_rev)
        ], JsonResponse::HTTP_CREATED);
    }


    /**
     * Display the specified resource for Books and Book Reviews.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showBook($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'error' => 'Book not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new BookResource($book);
    }
    // getting all book reviews
    public function bookReview(int $bookId)
    {
        // @TODO implement
        // getting the book
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json([
                'error' => 'Book not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if (sizeof($book->reviews) < 1) {
            return response()->json([
                'error' => 'No book reviews found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $bookRevs = array();

        foreach ($book->reviews as $bkrv) {
            $bookRevs[] = array(
                'id'        => $bkrv->id,
                'review'    => $bkrv->review,
                'comment'   => $bkrv->comment,
                'user'      => array(
                    'id'    => $bkrv->user_id ? User::where('id',$bkrv->user_id)->first()->id : '',
                    'name'  => $bkrv->user_id ? User::where('id',$bkrv->user_id)->first()->name : ''
                )
            );
        }

        return response()->json([
            'data' => $bookRevs
        ], JsonResponse::HTTP_OK);

        // return BookReviewCollection::collection(BookReview::paginate(15));   
    }

    // parttwo
    public function showBookReview($id)
    {
        $book_review = BookReview::find($id);

        if (!$book_review) {
            return response()->json([
                'error' => 'Book review not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new BookReviewResource($book_review);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBook(int $bookId, PostBookRequest $request, $id)
    {
        $book_review = BookReview::find($id);

        if (!$book_review) {
            return response()->json([
                'error' => 'Book review not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $book_review->update($request->all());

        return response()->json([
            'data' => new BookResource($book_review)
        ], JsonResponse::HTTP_ACCEPTED);
    }

    // parttwo
    public function updateBookReview(PostBookReviewRequest $request, $id)
    {
        $book_review = BookReview::find($id);

        if (!$book_review) {
            return response()->json([
                'error' => 'Book review not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $book_review->update($request->all());

        return response()->json([
            'data' => new BookReviewResource($book_review)
        ], JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        
        if (!$book) {
            return response()->json([
                'error' => 'Book not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $book->delete();
        
        return response()->json(
            ['message' => 'Book deleted successfully.'],
            JsonResponse::HTTP_PARTIAL_CONTENT
        );
    }

    // part two
    public function deatroyBookReview(int $bookId, $id)
    {
        $book_review = BookReview::find($id);
        
        if (!$book_review) {
            return response()->json([
                'error' => 'Book review not found!'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $book_review->delete();
        
        return response()->json(
            ['message' => 'Book review deleted successfully.'],
            JsonResponse::HTTP_PARTIAL_CONTENT
        );
    }
}
