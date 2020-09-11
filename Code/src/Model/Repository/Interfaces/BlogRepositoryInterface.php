<?php

declare(strict_types=1);

namespace App\Model\Repository\Interfaces;

use App\Model\Entity\Post;

interface BlogRepositoryInterface
{

    /* Read */
    public function readAllPost(int $page, int $perPage, string $side): array;
    public function lastPost(): ?Object;
    public function findById(int $id): ?post;
    
    /* CUD */
    public function create(Post $post): bool;
    public function update(Post $post): bool;
    public function delete(Post $post): bool;

    /* Count */ 
    public function count(string $side): ?string;

}
