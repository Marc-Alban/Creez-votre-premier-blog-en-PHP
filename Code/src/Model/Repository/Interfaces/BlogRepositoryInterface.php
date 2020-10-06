<?php

declare(strict_types=1);

namespace App\Model\Repository\Interfaces;

interface BlogRepositoryInterface
{

    /* Read */
    public function readAllPost(int $page, int $perPage): array;

    /* Count */ 
    public function count(): ?string;

}
