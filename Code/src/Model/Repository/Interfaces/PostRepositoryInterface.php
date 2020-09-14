<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;
use App\Service\Database;
use App\Model\Entity\Post;

interface PostRepositoryInterface
{

    /* Read */
    public function findById(int $id): ?post;
    
    /* CUD */
    public function create(Post $post): bool;
    public function update(Post $post): bool;
    public function delete(Post $post): bool;

}
