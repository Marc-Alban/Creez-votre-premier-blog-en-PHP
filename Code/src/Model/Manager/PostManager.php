<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\PostRepositoryInterface;

final class PostManager
{
    private PostRepositoryInterface $postRepo;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepo = $postRepository;
    }

    public function showOne(int $id): ?Post
    {
        // exemple de régle de gestion fictif
        if ($id > 600) {
            return null;
        }

        return $this->postRepo->findById($id);
    }
}
