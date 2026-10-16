<?php

declare(strict_types=1);

namespace App\Domain\User\DTOs;

final readonly class UserProfileData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $avatarUrl = null,
    ) {}

    /**
     * @param  array{name: string, email: string, phone?: string|null, avatar_url?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name']),
            email: strtolower(trim($data['email'])),
            phone: isset($data['phone']) ? trim($data['phone']) : null,
            avatarUrl: isset($data['avatar_url']) ? trim($data['avatar_url']) : null,
        );
    }
}
