<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;
use App\Model\Entity\User;

interface UserRepositoryInterface
{

    /* Read */
    public function joinUserPost(int $id): ?User;
    
    /* CUD */
    public function createUser(User $user): bool;
    public function updateUser(User $user): bool;
    public function deleteUser(User $user): bool;

}
