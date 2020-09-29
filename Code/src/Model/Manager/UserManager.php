<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;


final class UserManager
{
    private UserRepository $userRepo;

    public function __construct(array $dataManager)
    {
        $this->userRepo = $dataManager['repository']['repoAdd'];
    }

    public function findUser(int $user): string
    {
        return $this->userRepo->getUser($user);
    }

}