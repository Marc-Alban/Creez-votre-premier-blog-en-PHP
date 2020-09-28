<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Entity\User;


final class UserManager
{

    public function findUser(User $user, string $method)
    {
        $this->user->getUser($user, $method);
    }

}