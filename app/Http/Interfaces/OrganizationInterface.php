<?php

namespace App\Http\Interfaces;


use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface OrganizationInterface
{
    public function index(Request $request);

    public function adminIndex(Request $request, bool $is_branch = false);

    public function show(Request $request, int $id): JsonResponse;

    public function store(Request $request, bool $is_branch = false): JsonResponse;

    public function update(Request $request, Organization $organization, bool $is_branch = false): JsonResponse;

    public function destroy(Organization $organization,  bool $is_branch = false): JsonResponse;
}
