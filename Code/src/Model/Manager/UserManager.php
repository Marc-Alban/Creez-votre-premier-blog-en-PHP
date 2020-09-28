<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Repository\PostRepository;


final class UserManager
{
    private PostRepository $postRepo;

    public function findUser(int $user): void
    {
        var_dump('test1', $this->postRepo);
        die();
            $this->postRepo->getUser($user);
    }

}