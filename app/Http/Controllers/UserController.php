<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $hashedPassword = Hash::make(Arr::get($data, 'password'));

        $user = User::create([
            'name' => Arr::get($data, 'name'),
            'password' => $hashedPassword,
            'email' => Arr::get($data, 'email'),
        ]);

        return UserResource::make($user);
    }

    /**
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    /**
     * @param UpdateUserRequest $updateUserRequest
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $updateUserRequest, User $user)
    {
        $user->update($updateUserRequest->validated());

        return UserResource::make($user);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }


    public function login(LoginUserRequest $request)
    {

        $data = $request->validated();

        $user = User::where('email', Arr::get($data, 'email'))->first();

        if (blank($user) || !Hash::check(Arr::get($data, 'password'), $user->password)) {
            return response()->json(['Unauthorized'], 401);
        }

        $token = $user->createToken('api_token', [], now()->addMinutes(720));

        return response()->json([
            'message' => 'Logged in successfully',
            'access_token' => $token->plainTextToken,
            '$user_id' => $user
        ], 200);

    }


}
