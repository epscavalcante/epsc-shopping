<?php

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Account;

interface AccountRepository
{
    public function getById(string $id): ?Account;
}