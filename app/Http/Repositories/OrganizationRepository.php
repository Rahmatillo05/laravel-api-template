<?php

namespace App\Http\Repositories;


use App\Http\Interfaces\OrganizationInterface;
use App\Http\Resources\DefaultResource;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Repositories\BaseRepository;
use Illuminate\Support\Str;

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

    public function adminIndex(Request $request, bool $is_branch = false)
    {
        $query = $this->generateQuery($request);
        if ($is_branch) {
            $query->where('parent_id', Auth::user()->organization_id);
        } else {
            $query->where('parent_id', null);
        }
        $data = $query->paginate($request->get('per_page'));
        return DefaultResource::collection($data);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $query = $this->generateQuery($request);
        $organization = $query->findOrFail($id);
        $this->defaultAppendAndInclude($organization, $request);
        return okResponse($organization);
    }

    public function store(Request $request, bool $is_branch = false): JsonResponse
    {
        $user = Auth::user();
        if (!$is_branch) {
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
        } else {
            if ($user->role != User::ROLE_ORGANIZATION) {
                return errorResponse('You are not authorized to create a branch');
            }
            $request['parent_id'] = $user->organization_id;
            $model = Organization::query()->create($request->all());
            $this->defaultAppendAndInclude($model, $request);
            return createdResponse($model);
        }

    }

    public function update(Request $request, Organization $organization, bool $is_branch = false): JsonResponse
    {
        if ($is_branch) {
            $request['parent_id'] = Auth::user()->organization_id;
            $organization->update($request->all());
            $this->defaultAppendAndInclude($organization, $request);
            return okResponse($organization);
        }
        $organization->update($request->all());
        $this->defaultAppendAndInclude($organization, $request);
        return okResponse($organization);
    }

    public function destroy(Organization $organization, bool $is_branch = false): JsonResponse
    {
        if ($is_branch && $organization->parent_id) {
            $organization->delete();
            return okResponse('Branch deleted');
        }
        $organization->delete();
        return okResponse($organization);
    }

    public function detail(Request $request): JsonResponse
    {
        $organization = Auth::user()->organization;
        if ($request->filled('organization_id')) {
            $organization = $this->modelClass::query()->firstOrFail($request->get('organization_id'));
        }

        if ($request->filled('name')) {
            $organization->name = $request->get('name');
        }
        if ($request->filled('latitude')) {
            $organization->latitude = $request->get('latitude');
        }
        if ($request->filled('longitude')) {
            $organization->longitude = $request->get('longitude');
        }
        $organization->save();
        try {
            DB::beginTransaction();
            if ($request->filled('file_ids')) {
                $organization->files()->sync($request->get('file_ids'));
                unset($request['file_ids']);
            }
            $organization->detail()->updateOrCreate(
                ['organization_id' => $organization->id],
                $request->all()
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse($e->getMessage());
        }
        $this->defaultAppendAndInclude($organization, $request);
        return okResponse($organization);
    }
}

