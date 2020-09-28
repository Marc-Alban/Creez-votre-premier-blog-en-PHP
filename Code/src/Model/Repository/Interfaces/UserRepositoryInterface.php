<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;
use App\Model\Entity\User;

interface UserRepositoryInterface
{

    /* Read */
    public function getEmail(User $user): ?User;
    public function getUser(User $user, string $methode): ?User;
    public function getPassword(User $user): ?User;
    
    /* CUD */
    public function createUser(array $data): void;
    public function updateUser(User $user): bool;
    public function deleteUser(User $user): bool;

}
