<?php

declare(strict_types=1);

namespace App\Model\Manager\Frontoffice;

use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\Frontoffice\PostRepositoryInterface;

final class PostManager
{
    private PostRepositoryInterface $postRepo;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepo = $postRepository;
    }

    public function showOne(string $dataId): ?Post
    {
        $id = intval($dataId);
        // exemple de régle de gestion fictif
        if ($id > 600) {
            return null;
        }
        return $this->postRepo->findById($id);
    }
}
