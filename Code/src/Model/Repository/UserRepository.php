<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\User;
use App\Model\Repository\Interfaces\UserRepositoryInterface;
final class UserRepository implements UserRepositoryInterface
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }
    
    public function joinUserPost(int $idPost): ?User
    {
        return null;
    }

    public function createUser(User $user) : bool
    {
        return false;
    }

    public function updateUser(User $user) : bool
    {
        return false;
    }

    public function deleteUser(User $user) : bool
    {
        return false;
    }

}
