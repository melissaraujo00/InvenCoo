<?php

namespace App\Actions\User;

use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRolesAction
{
    public function execute(User $user, array $roleIds): void
    {
        if (empty($roleIds)) {
            $user->syncRoles([]);
            return;
        }

        // Convertir IDs a nombres de roles
        $roleNames = Role::whereIn('id', $roleIds)
            ->pluck('name')
            ->toArray();

        $user->syncRoles($roleNames);
    }
}
