<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

interface UpdatePostRepositoryInterface
{
    public function updatePost(): ?array;
}
