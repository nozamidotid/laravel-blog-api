<?php

namespace App\Http\Controllers\AUth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data["password"]);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::where("username", $data["username"])->first();

        if (!$user || !Hash::check($data["password"], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password wrong!"
                    ]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function getCurrentUser(Request $request): UserResource
    {
        $user = Auth::user();

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $user = $request->user();

        $data = $request->validated();

        if ($data["name"]) {
            $user->name = $data["name"];
        }
        if ($data["password"]) {
            $user->password = Hash::make($data["password"]);
        }

        $user->save();

        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var App\Models\User $user */
        $user = $request->user();
        $user->token = null;
        $user->save();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
