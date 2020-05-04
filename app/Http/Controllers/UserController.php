<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// adding models
use App\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;

use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display the constructor of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        // $this->middleware('auth:api')->except('index','show','store');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (sizeof(User::all()) < 1) {
            return response()->json([
                'error' => 'No user found'
            ], Response::HTTP_NOT_FOUND);
        }
        return UserCollection::collection(User::paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->email_verified_at    = $request->email_verified_at;
        $user->api_token    = $request->api_token;
        $user->is_admin    = $request->is_admin;
        $user->save();

        return response()->json([
            'message' => 'User added successfully',
            'data'  => new UserResource($user)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'User not found!!'
            ], Response::HTTP_NOT_FOUND);
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'User not found!'
            ], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->all());

        return response()->json([
            'data' => new UserResource($user)
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'error' => 'User account not found!'
            ], Response::HTTP_NOT_FOUND);
        }

        $user->delete();
        
        return response()->json(
            ['message' => 'User account deleted successfully.'],
            Response::HTTP_PARTIAL_CONTENT
        );
    }
}
