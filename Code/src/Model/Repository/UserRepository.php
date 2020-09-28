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


    public function getEmail(User $user): ?User
    {
        return null;
    }


    public function getUser(int $user): ?User
    {
        return null;
    }

    public function getPassword(User $user): ?User
    {
        return null;
    }


    public function createUser(array $data): void
    {
        $tabUser = [
            ':userName' => htmlspecialchars(trim($data['post']['userName'])),
            ':email' => htmlspecialchars(trim($data['post']['email'])),
            ':passwordUser' => password_hash($data['post']['password'], PASSWORD_DEFAULT),
        ];
        $req = $this->db->prepare("INSERT INTO user (userName, email, passwordUser) VALUES (:userName, :email, :passwordUser)");
        $req->execute($tabUser);
    }

    public function updateUser(User $user): bool
    {
        return false;
    }

    public function deleteUser(User $user): bool
    {
        return false;
    }

}
