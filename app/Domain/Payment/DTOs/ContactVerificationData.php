<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

readonly class ContactVerificationData
{
    public function __construct(
        public bool $tenantContacted,
        public bool $ownerContacted,
        public bool $tenantConfirmed,
        public bool $ownerConfirmed,
        public ?string $adminNotes = null,
    ) {}

    public function areBothPartiesConfirmed(): bool
    {
        return $this->tenantContacted
            && $this->ownerContacted
            && $this->tenantConfirmed
            && $this->ownerConfirmed;
    }
}
