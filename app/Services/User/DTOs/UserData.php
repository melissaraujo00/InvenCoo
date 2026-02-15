<?php

namespace App\Services\User\DTOs;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly string $number,
        public readonly int $office_id,
        public readonly ?string $password = null,
        public readonly ?bool $status = true,
        public readonly ?array $roles = []
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            last_name: $data['last_name'],
            email: $data['email'],
            number: $data['number'],
            office_id: $data['office_id'],
            password: $data['password'] ?? null,
            status: $data['status'] ?? true,
            roles: $data['roles'] ?? []
        );
    }
}
