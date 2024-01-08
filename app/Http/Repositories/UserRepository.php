<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\UserInterface;
use App\Http\Resources\DefaultResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserRepository extends BaseRepository implements UserInterface
{
    /**
     * @var User
     */
    protected mixed $modelClass = User::class;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->generateQuery($request);
        $data = $query->paginate($request->get('per_page'));

        return DefaultResource::collection($data);
    }

    public function adminIndex(Request $request): AnonymousResourceCollection
    {
        $query = $this->generateQuery($request);
        $data = $query->paginate($request->get('per_page'));

        return DefaultResource::collection($data);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $query = $this->generateQuery($request);
        $user = $query->findOrFail($id);

        return okResponse($user);
    }

    public function store(Request $request): JsonResponse
    {
        $model = User::query()->create($request->all());
        $this->defaultAppendAndInclude($model, $request);

        return createdResponse($model);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $user = $user->update($request->all());
        $this->defaultAppendAndInclude($user, $request);

        return okResponse($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return okResponse($user);
    }
}
