<?php

namespace App\Http\Interfaces;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface UserInterface
{
    public function index(Request $request);

    public function adminIndex(Request $request);

    public function show(Request $request, int $id): JsonResponse;

    public function store(Request $request): JsonResponse;

    public function update(Request $request, User $user): JsonResponse;

    public function destroy(User $user): JsonResponse;
}
