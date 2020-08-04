<?php

declare(strict_types=1);

namespace App\Model\Repository\Interfaces;

use App\Model\Entity\Post;

interface PostRepositoryInterface
{
    /* Read */
    public function findByAll() : ?array;
    public function findById(int $id) : ?Post;
    
    /* CUD */
    public function create(Post $post) : bool;
    public function update(Post $post) : bool;
    public function delete(Post $post) : bool;
}
