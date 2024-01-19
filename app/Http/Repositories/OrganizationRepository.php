<?php

namespace App\Http\Repositories;


use App\Http\Interfaces\OrganizationInterface;
use App\Http\Resources\DefaultResource;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Repositories\BaseRepository;

class OrganizationRepository extends BaseRepository implements OrganizationInterface
{
    /**
     * @var Organization $ modelClass
     */
    protected mixed $modelClass = Organization::class;

    public function index(Request $request)
    {
        $query = $this->generateQuery($request);
        $data = $query->paginate($request->get('per_page'));
        return DefaultResource::collection($data);
    }

    public function adminIndex(Request $request)
    {
        $query = $this->generateQuery($request);
        $data = $query->paginate($request->get('per_page'));
        return DefaultResource::collection($data);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $query = $this->generateQuery($request);
        $organization = $query->findOrFail($id);
        return okResponse($organization);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if ($user->organization_id && $user->role == User::ROLE_ORGANIZATION) {
            return errorResponse('You already have an organization');
        }
        $model = Organization::query()->create($request->all());
        if ($model instanceof Organization) {
            $user->update([
                'organization_id' => $model->id
            ]);
            $user->roles()->first()->update([
                'role' => User::ROLE_ORGANIZATION
            ]);
        }
        $token = $user->createToken($model->name . ' Token', [User::ROLE_ORGANIZATION])->accessToken;
        $this->defaultAppendAndInclude($model, $request);
        return createdResponse([
            'organization' => $model,
            'token' => $token
        ]);
    }

    public function update(Request $request, Organization $organization): JsonResponse
    {
        $organization = $organization->update($request->all());
        $this->defaultAppendAndInclude($organization, $request);
        return okResponse($organization);
    }

    public function destroy(Organization $organization): JsonResponse
    {
        $organization->delete();
        return okResponse($organization);
    }
}

