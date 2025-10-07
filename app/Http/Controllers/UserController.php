<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->has('page')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'admin');
            });

            return UserResource::collection($query->paginate(15));
        }

        return response()->json(UserResource::collection($query->get())->resolve());
    }

    public function store(UserRequest $request): UserResource
    {
        return new UserResource(User::create($request->validated()));
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->noContent();
    }
}
