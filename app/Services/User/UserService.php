<?php

namespace App\Services\User;

use App\Models\User;
use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\GetUsersListAction;
use App\Services\User\DTOs\UserData;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly GetUsersListAction $getUsersListAction
    ) {}

    public function getUsersList(?string $search = null): LengthAwarePaginator
    {
        return $this->getUsersListAction->execute($search);
    }

    public function createUser(UserData $userData): User
    {
        return $this->createUserAction->execute($userData);
    }

    public function updateUser(User $user, UserData $userData): User
    {
        return $this->updateUserAction->execute($user, $userData);
    }

    public function deleteUser(User $user): bool
    {
        return $this->deleteUserAction->execute($user);
    }
}
