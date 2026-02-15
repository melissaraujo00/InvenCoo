<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\User\UserService;
use App\Services\User\DTOs\UserData;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index()
    {
        $users = $this->userService->getUsersList(request('search'));

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $offices = Office::all();
        $roles = Role::all();

        return view('pages.users.create', compact('offices', 'roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $userData = UserData::fromRequest($request->validated());

        $this->userService->createUser($userData);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $offices = Office::all();
        $roles = Role::all();
        $userRoleIds = $user->roles->pluck('id')->toArray();

        return view('pages.users.edit', compact('user', 'offices', 'roles', 'userRoleIds'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $userData = UserData::fromRequest($request->validated());

        $this->userService->updateUser($user, $userData);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
