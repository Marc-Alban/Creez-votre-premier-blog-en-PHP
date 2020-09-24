<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\Post;
use App\Model\Entity\User;
use App\Model\Repository\UserRepository;

final class UserManager
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $UserRepository)
    {
        $this->userRepository = $UserRepository;
    }

    public function findUserById($obj,$method)
    {
        $post = new $obj;
        $test = $post->$method;
    }

}
