<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

interface PasswordRepositoryInterface 
{
    public function updatePassBdd(string $pass, int $userId): void;
}
