<?php

declare(strict_types=1);

namespace App\Domain\User\DTOs;

use App\Domain\User\Enums\UserRole;

final readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public UserRole $role = UserRole::TENANT,
        public ?string $phone = null,
    ) {}

    /**
     * @param  array{name: string, email: string, password: string, role?: string|null, phone?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        $role = isset($data['role']) && $data['role'] !== ''
            ? (UserRole::tryFrom($data['role']) ?? UserRole::TENANT)
            : UserRole::TENANT;

        return new self(
            name: trim($data['name']),
            email: strtolower(trim($data['email'])),
            password: $data['password'],
            role: $role,
            phone: isset($data['phone']) ? trim($data['phone']) : null,
        );
    }
}
