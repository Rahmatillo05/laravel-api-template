<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Interfaces\UserInterface;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group User
 */
class UserController extends Controller
{
    public function __construct(public UserInterface $userRepository)
    {
    }

    /**
     * User Get all
     *
     * @response {
     *  "updated_at": "date",
     *  "email_verified_at": "date",
     *  "created_at": "date",
     *  "id": "integer",
     *  "remember_token": "string",
     *  "password": "string",
     *  "username": "string",
     *  "phone": "string",
     *  "email": "string",
     * }
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return $this->userRepository->index($request);
    }

    /**
     * User adminIndex get All
     *
     * @response {
     *  "updated_at": "date",
     *  "email_verified_at": "date",
     *  "created_at": "date",
     *  "id": "integer",
     *  "remember_token": "string",
     *  "password": "string",
     *  "username": "string",
     *  "phone": "string",
     *  "email": "string",
     * }
     *
     * @return JsonResponse
     */
    public function adminIndex(Request $request)
    {
        return $this->userRepository->adminIndex($request);
    }

    /**
     * User view
     *
     * @queryParam id required
     *
     * @response {
     *  "updated_at": "date",
     *  "email_verified_at": "date",
     *  "created_at": "date",
     *  "id": "integer",
     *  "remember_token": "string",
     *  "password": "string",
     *  "username": "string",
     *  "phone": "string",
     *  "email": "string",
     * }
     */
    public function show(Request $request, int $id): JsonResponse
    {
        return $this->userRepository->show($request, $id);
    }

    /**
     * User create
     *
     * @bodyParam email_verified_at date
     * @bodyParam remember_token string
     * @bodyParam password string
     * @bodyParam username string
     * @bodyParam phone string
     * @bodyParam email string
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        return $this->userRepository->store($request);
    }

    /**
     * User update
     *
     * @queryParam user required
     *
     * @bodyParam email_verified_at date
     * @bodyParam remember_token string
     * @bodyParam password string
     * @bodyParam username string
     * @bodyParam phone string
     * @bodyParam email string
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        return $this->userRepository->update($request, $user);
    }

    /**
     * User delete
     *
     * @queryParam user required
     */
    public function destroy(User $user): JsonResponse
    {
        return $this->userRepository->destroy($user);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = \Auth::user();
        if ($request->filled('include')) {
            $user = $user->load(explode(',', $request->get('include')));
        }
        if ($request->filled('append')) {
            $user = $user->append(explode(',', $request->get('append')));
        }
        return okResponse([
            'user' => $user,
            'token' => $request->bearerToken(),
        ]);
    }
}
