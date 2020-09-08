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

    public function showOne(?string $dataId): ?Post
    {
        $id = intval($dataId);

        if ($id > 600) {
            return null;
        }
        else if($id === null || empty($id))
        {
            return null;
        }
        return $this->postRepo->findById($id);
    }
}
