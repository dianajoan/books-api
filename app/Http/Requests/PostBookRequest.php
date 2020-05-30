<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
// adding response code
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostBookRequest extends FormRequest
{
    // adding new rule

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'isbn'      => 'required|min:13|max:13|unique:books',
            'title'     => 'required',
            'description' => 'required',
            'authors'   => 'required|array|min:1',
            'authors.0' => 'integer'
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
            'isbn.required' => 'The isbn field is required',
            'title.required' => 'The title field is required',
            'description.required' => 'The description field is required',
            'authors.required' => 'You need to add atleat one author from the existing',
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

        throw new HttpResponseException(response()->json([
            'errors' => $errors
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

}
