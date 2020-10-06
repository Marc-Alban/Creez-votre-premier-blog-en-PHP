<?php
declare(strict_types=1);
namespace App\Model\Repository\Interfaces;

use App\Model\Entity\Post;


interface PostRepositoryInterface 
{
    public function findById(int $id): ?Post;
}
