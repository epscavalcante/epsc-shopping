<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

class Account
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $name,
        public readonly string $email,
    ) {}
}
