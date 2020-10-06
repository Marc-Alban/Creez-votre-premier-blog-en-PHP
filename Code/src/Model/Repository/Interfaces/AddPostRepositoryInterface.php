<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

interface AddPostRepositoryInterface
{
    public function createPost(array $dataForm): ?array;
}
