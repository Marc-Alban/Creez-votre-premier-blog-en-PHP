<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

interface DashboardRepositoryInterface 
{
    public function updateUserBdd(array $data, int $idUser): void;
}
