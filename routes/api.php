<?php

use Illuminate\Http\Request;

// adding response for codes and messages
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// adding the endpoint for logged in user
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// adding endpoint to check for successful load
Route::get('/', [
	'as' 	=> 'start',
	'uses' 	=> function(){
		return response()->json([
            'message'=>'Welcome to the ' . config('app.name', 'Books API By Diana Joanita')
        ], Response::HTTP_PARTIAL_CONTENT)->header('Content-Type', 'application/json');
	}
]);

// endpoints to show system users
// created new resource controller for users
Route::apiResource('/users','UserController');



// // remove name('books.index');
Route::get('/books', 'BooksController@getCollection')->name('books.index');
// // remove name('books.store')
Route::post('/books', 'BooksController@post')->name('books.store');//->middleware('auth.admin');
// // to be continued
Route::post('/books/{bookId}/reviews', 'BooksController@postReview')->name('reviews.store');



// our added endpoints for the system 
Route::get('/books/{id}', 'BooksController@showBook')->name('books.show');
Route::delete('/books/{id}','BooksController@deleteBook')->name('books.destroy');
Route::put('/books/{id}','BooksController@updateBook')->name('books.update');
Route::patch('/books/{id}','BooksController@updateBook')->name('books.update');

Route::get('/books/{bookId}/reviews', 'BooksController@bookReview')->name('books.reviews');

Route::get('/books/{bookId}/reviews/{id}', 'BooksController@showBookReview')->name('reviews.show');
Route::patch('/books/{bookId}/reviews/{id}', 'BooksController@updateBookReview')->name('reviews.update');
Route::put('/books/{bookId}/reviews/{id}', 'BooksController@updateBookReview')->name('reviews.update');
Route::delete('/books/{bookId}/reviews/{id}', 'BooksController@deatroyBookReview')->name('reviews.destroy');
