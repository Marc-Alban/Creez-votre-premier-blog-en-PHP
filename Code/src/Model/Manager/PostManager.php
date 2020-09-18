<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Entity\Post;
use App\Model\Entity\User;
use App\Model\Repository\PostRepository;

final class PostManager
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
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
        return $this->postRepository->findById($id);
    }

    public function showUser(?string $id ): ?User 
    {
        $idPost = intval($id);
        return $this->postRepository->joinUserPost($idPost);
    }
}
