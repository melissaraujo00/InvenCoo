<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUsersListAction
{
    public function execute(?string $search = null): LengthAwarePaginator
    {
        return User::with(['office', 'roles'])
            ->when($search, fn($query) =>
                $query->whereAny(['name', 'last_name', 'email', 'number'], 'LIKE', "%{$search}%")
            )
            ->paginate(10)
            ->withQueryString();
    }
}
