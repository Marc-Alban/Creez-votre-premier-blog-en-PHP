<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

use App\Model\Entity\User;

interface UserRepositoryInterface
{

    /* Read */
    public function getEmailBdd(string $email): ?string;
    public function getUser(int $user = null): ?string;
    public function getIdUser(): ?int;
    public function getPassword(string $email): ?string;
    
    /* CUD */
    public function createUser(array $data): void;
    public function updateUser(User $user): bool;
    public function deleteUser(User $user): bool;

}
