<?php

declare(strict_types=1);

namespace App\Model\Repository\Interfaces;

interface BlogRepositoryInterface
{

    /* Read */
    public function readAllPost(int $page, int $perPage, string $side): array;
    public function lastPost(): ?Object;

    /* Count */ 
    public function count(string $side): ?string;

}