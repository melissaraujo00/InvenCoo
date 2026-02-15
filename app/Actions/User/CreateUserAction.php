<?php

namespace App\Actions\User;

use App\Models\User;
use App\Services\User\DTOs\UserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateUserAction
{
    private AssignRolesAction $assignRolesAction;

    public function __construct(AssignRolesAction $assignRolesAction)
    {
        $this->assignRolesAction = $assignRolesAction;
    }

    public function execute(UserData $userData): User
    {
        return DB::transaction(function () use ($userData) {
            // Crear el usuario
            $user = User::create([
                'name' => $userData->name,
                'last_name' => $userData->last_name,
                'email' => $userData->email,
                'number' => $userData->number,
                'office_id' => $userData->office_id,
                'password' => Hash::make($userData->password),
                'status' => $userData->status,
            ]);

            // Asignar roles si existen
            if (!empty($userData->roles)) {
                $this->assignRolesAction->execute($user, $userData->roles);
            }

            return $user;
        });
    }
}
