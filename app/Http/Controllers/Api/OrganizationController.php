<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Interfaces\OrganizationInterface;
use App\Http\Requests\Organization\StoreOrganizationDetailRequest;
use App\Http\Requests\Organization\StoreOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Organization
 *
 */
class OrganizationController extends Controller
{

    public function __construct(public OrganizationInterface $organizationRepository)
    {
    }

    /**
    * Organization Get all
    *
    * @response {
    *  "id": "integer",
     *  "status": "integer",
     *  "created_at": "date",
     *  "updated_at": "date",
     *  "name": "string",
    * }
    * @return JsonResponse
    */

    public function index(Request $request)
    {
        return $this->organizationRepository->index($request);
    }

    /**
    * Organization adminIndex get All
    *
    * @response {
    *  "id": "integer",
     *  "status": "integer",
     *  "created_at": "date",
     *  "updated_at": "date",
     *  "name": "string",
    * }
    * @return JsonResponse
    */

    public function adminIndex(Request $request)
    {
        return $this->organizationRepository->adminIndex($request);
    }

    /**
    * Organization view
    *
    * @queryParam id required
    *
    * @param Request $request
    * @param int     $id
    * @return JsonResponse
    * @response {
    *  "id": "integer",
     *  "status": "integer",
     *  "created_at": "date",
     *  "updated_at": "date",
     *  "name": "string",
    * }
    */

    public function show(Request $request, int $id): JsonResponse
    {
        return $this->organizationRepository->show($request, $id);
    }

    /**
    * Organization create
    *
         * @bodyParam status integer
     * @bodyParam name string

    *
    * @param StoreOrganizationRequest $request
    * @return JsonResponse
    */

    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        return $this->organizationRepository->store($request);
    }

    /**
    * Organization update
    *
    * @queryParam organization required
    *
         * @bodyParam status integer
     * @bodyParam name string

    *
    * @param UpdateOrganizationRequest $request
    * @param Organization $organization
    * @return JsonResponse
    */

    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
         return $this->organizationRepository->update($request, $organization);
    }

    /**
     * Organization delete
     *
     * @queryParam organization required
     *
     * @param Organization $organization
     * @return JsonResponse
     */

    public function destroy(Organization $organization): JsonResponse
    {
        return  $this->organizationRepository->destroy($organization);
    }

    public function myOrganization(Request $request)
    {
        $organization_id = Auth::user()->organization_id;

        return $this->organizationRepository->show($request, $organization_id);
    }

    public function details(StoreOrganizationDetailRequest $request)
    {
        return $this->organizationRepository->detail($request);
    }

    public function branches(Request $request)
    {
        return $this->organizationRepository->adminIndex($request, true);
    }

    public function storeBranch(StoreOrganizationRequest $request)
    {
        return $this->organizationRepository->store($request, true);
    }

    public function updateBranch(UpdateOrganizationRequest $request, Organization $organization)
    {
        return $this->organizationRepository->update($request, $organization, true);
    }

    public function destroyBranch(Organization $organization)
    {
        return $this->organizationRepository->destroy($organization, true);
    }
}
