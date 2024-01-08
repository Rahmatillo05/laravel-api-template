<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\AuthRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(public AuthRepository $authRepository)
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:12|max:12',
            'password' => 'required|string|min:8',
        ]);
        return $this->authRepository->login($request);
    }

    public function register(Request $request)
    {
        $request->validate([
            'phone' => 'required|unique:users,phone|min:12|max:12',
        ]);
        return $this->authRepository->register($request);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'key' => 'required|string|min:20|max:20',
        ]);
        try {
            return $this->authRepository->confirm($request);
        } catch (\Exception $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    public function createPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        try {
            return $this->authRepository->createPassword($request);
        } catch (\Exception $exception) {
            return errorResponse($exception->getMessage());
        }
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'key' => 'required|string|min:20|max:20',
        ]);
        return $this->authRepository->resendCode($request);
    }


}
