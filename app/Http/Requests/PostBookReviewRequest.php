<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// adding response code
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostBookReviewRequest extends FormRequest
{
    // adding authorisation to true 

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // makings authorisation true to API posts
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // @TODO implement
            // 'book_id'   => 'required|integer',
            'user_id'   => 'required|integer',
            'review'    => 'required|integer',
            'comment'   => 'required|string'
        ];
    }

    /**
     * Get the error messages that apply to the request parameters.
     *
     * @return array
     */
    public function messages()
    {
        return [
            // 'book_id.required' => 'A correct book id field is required',
            'user_id.required' => 'The correct user id field is required',
            'review.required' => 'The review field is required',
            'comment.required' => 'You need to add atleat one author from the existing',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(['errors' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

 
}
