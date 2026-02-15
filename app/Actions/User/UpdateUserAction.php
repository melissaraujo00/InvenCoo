<?php

namespace App\Actions\User;

use App\Models\User;
use App\Services\User\DTOs\UserData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    private AssignRolesAction $assignRolesAction;

    public function __construct(AssignRolesAction $assignRolesAction)
    {
        $this->assignRolesAction = $assignRolesAction;
    }

    public function execute(User $user, UserData $userData): User
    {
        return DB::transaction(function () use ($user, $userData) {
            $updateData = [
                'name' => $userData->name,
                'last_name' => $userData->last_name,
                'email' => $userData->email,
                'number' => $userData->number,
                'office_id' => $userData->office_id,
                'status' => $userData->status,
            ];

            // Solo actualizar contraseña si se proporcionó una nueva
            if ($userData->password) {
                $updateData['password'] = Hash::make($userData->password);
            }

            $user->update($updateData);

            // Actualizar roles si se enviaron
            $this->assignRolesAction->execute($user, $userData->roles);

            return $user->fresh();
        });
    }
}
