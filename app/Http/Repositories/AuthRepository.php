<?php

namespace App\Http\Repositories;

use App\Http\Services\SmsService;
use App\Models\ConfirmCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function register(Request $request): JsonResponse
    {
        $model = User::create([
            'phone' => $request->phone,
            'status' => User::STATUS_WAIT_VERIFICATION,
        ]);

        return okResponse([
            'user' => $model,
            'key' => $this->sendCode($model),
        ]);
    }

    protected function sendCode(User $user): string
    {
        $code = rand(111111, 999999);
        $key = \Str::random(20);
        $confirmCode = ConfirmCode::create([
            'code' => $code,
            'key' => $key,
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(5),
        ]);
        $text = "Your verification code is: {$code}";
        SmsService::sendSms($user->phone, $text);

        return $confirmCode->key;
    }

    public function confirm(Request $request): JsonResponse
    {
        $confirmCode = ConfirmCode::where('key', $request->key)
            ->where('code', $request->code)
            ->firstOrFail();
        if (now()->gt($confirmCode->expires_at)) {
            return errorResponse('Code expired');
        } elseif ($confirmCode->is_used) {
            return errorResponse('Code already used');
        }
        if (in_array($confirmCode->user->status, [User::STATUS_WAIT_VERIFICATION, User::STATUS_CREATING_PASSWORD])) {
            $confirmCode->update([
                'is_used' => true,
            ]);
            $confirmCode->user->update([
                'status' => User::STATUS_CREATING_PASSWORD,
            ]);

            return okResponse($confirmCode->user);
        } elseif ($confirmCode->user->status === User::STATUS_ACTIVE) {
            $confirmCode->update([
                'is_used' => true,
            ]);

            return okResponse([
                'user' => $confirmCode->user,
                'token' => $confirmCode->user->createToken('authToken')->accessToken,
            ]);
        }

        return errorResponse('User not found');
    }

    public function createPassword(Request $request): JsonResponse
    {
        $model = User::findOrFail($request->user_id);
        if ($model->status !== User::STATUS_CREATING_PASSWORD) {
            return errorResponse('User status is not creating password');
        }
        $model->update([
            'password' => Hash::make($request->password),
            'status' => User::STATUS_ACTIVE,
        ]);

        return okResponse([
            'user' => $model,
            'token' => $model->createToken('authToken')->accessToken,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['phone' => $request->get('phone'), 'password' => $request->get('password')])) {
            $user = Auth::user();
            $token = $user->createToken($user->phone, [User::ROLE_USER])->accessToken;

            return okResponse([
                'user' => $user,
                'token' => $token,
            ]);
        } else {
            return errorResponse('Incorrect phone or password');
        }
    }

    public function resendCode(Request $request): JsonResponse
    {
        $confirmCode = ConfirmCode::where('key', $request->key)
            ->where('is_used', false)
            ->where('expires_at', '<', now())
            ->orderBy('created_at', 'desc')
            ->first();
        if ($confirmCode instanceof ConfirmCode) {
            $confirmCode->update([
                'is_used' => true,
            ]);

            return okResponse([
                'key' => $this->sendCode($confirmCode->user),
            ]);
        } else {
            return errorResponse('You have unused code');
        }

    }
}
