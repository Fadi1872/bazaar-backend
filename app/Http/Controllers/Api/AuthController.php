<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogInRequest;
use App\Http\Requests\RigisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Services\EloquentStorage;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthController extends Controller
{
    use AuthorizesRequests;
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService(new EloquentStorage(User::class));
    }

    /**
     * rigister the user 
     * 
     * @param RigisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RigisterRequest $request)
    {
        // try {
            //get the image ig provided
            $image = null;
            if ($request->hasFile('image'))
                $image = $request->file('image');

            //rigitring the user
            $data = $this->authService->rigister(
                collect($request->validated())->except('image')->toArray(),
                $image
            );
            return $this->successResponse("user rigistered successfully!", $data);
        // } catch (Throwable $e) {
        //     return $this->errorResponse("something went wrong!", 500);
        // }
    }

    /**
     * log the user in
     * 
     * @param LogInRequset $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LogInRequest $request)
    {
        try {
            $data = $this->authService->login($request->email, $request->password, $request->app_source);
            return $this->successResponse("You are logged in successfully!", $data);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 403);
        }
    }

    /**
     * log the user out
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
            return $this->successResponse("you are logged out successfully");
        } catch (Exception $e) {
            return $this->errorResponse("failed to delete the token!", 500);
        }
    }

    /**
     * return the user profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $data = (new UserResource($request->user()->load('image')))->toArray($request);
        return $this->successResponse("user profile detailes", $data);
    }

    /**
     * update user data
     * 
     * @param UpdateUserRequest $user, User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request) {
        try{
            $this->authService->update(Auth::user(), $request->validated());
            return $this->successResponse("user profile details updated successfully!", new UserResource(Auth::user()->load('image')));
        } catch(AuthorizationException $e) {
            return $this->errorResponse("something went wrong", 500);
        }
    }
}
