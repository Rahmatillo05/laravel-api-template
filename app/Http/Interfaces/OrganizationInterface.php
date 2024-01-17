<?php

namespace App\Http\Interfaces;


use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface OrganizationInterface
{
    public function index(Request $request);

    public function adminIndex(Request $request);

    public function show(Request $request, int $id): JsonResponse;

    public function store(Request $request): JsonResponse;

    public function update(Request $request, Organization $organization): JsonResponse;

    public function destroy(Organization $organization): JsonResponse;
}
