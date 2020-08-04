<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\PostRepositoryInterface;

final class PostRepository implements PostRepositoryInterface
{
    public function findById(int $id): ?Post
    {
        // *** exemple fictif d'accès à la base de données
        $data = ['id' => $id, 'title' => 'Article '. $id .' du blog', 'text' => 'Lorem ipsum'];

        // réfléchir à l'hydratation des entités;
        return new Post($data['id'], $data['title'], $data['text']);
    }

    public function findByAll(): ?array
    {
        return null;
    }

    public function create(Post $post) : bool
    {
        return false;
    }

    public function update(Post $post) : bool
    {
        return false;
    }

    public function delete(Post $post) : bool
    {
        return false;
    }
}
