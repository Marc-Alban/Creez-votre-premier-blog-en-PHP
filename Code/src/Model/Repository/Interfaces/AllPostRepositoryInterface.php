<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

interface AllPostRepositoryInterface
{
    public function deletePost(): ?array;
}


