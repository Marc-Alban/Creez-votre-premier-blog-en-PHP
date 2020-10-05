<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

use App\Model\Entity\Post;


interface PostRepositoryInterface 
{
    public function findById(int $id): ?Post;
    public function createPost(Post $post): void;
    public function updatePost(Post $post): bool;
    public function deletePost(Post $post): bool;
}
