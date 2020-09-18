<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;
use App\Model\Entity\Post;


interface PostRepositoryInterface 
{
    /* Read */
    public function findById(int $id): ?Post;
    /* CUD */
    public function createPost(Post $post): bool;
    public function updatePost(Post $post): bool;
    public function deletePost(Post $post): bool;
}
