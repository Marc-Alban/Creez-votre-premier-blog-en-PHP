<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Model\Repository\Interfaces\DashboardRepositoryInterface;
use App\Model\Repository\Interfaces\UserRepositoryInterface;

use App\Service\Database;

final class DashboardRepository implements DashboardRepositoryInterface, UserRepositoryInterface
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }


    /* Read */
    public function getEmailBdd(string $email): ?string
    {
        return null;
    }
    public function getAllFromUser(): ?User
    {
        $pdo = $this->db->query("SELECT * FROM user");
        $executeIsOk = $pdo->execute();

        if ($executeIsOk === true) {
            $userBdd = $pdo->fetchObject(User::class);
            if ($userBdd) {
                return $userBdd;
            } elseif ($userBdd === false) {
                return null;
            }
            return $userBdd;
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }
    public function updateUserBdd(array $data, int $idUser): void
    {
        
        $email = htmlentities(trim($data["post"]['email'])) ?? null;
        $userName = htmlentities(trim($data["post"]['userName'])) ?? null;
        $commentArray = [
            ':userName' => $userName,
            ':email' => $email,
            ':idUser' => $idUser,
        ];
        
        $req = $this->db->prepare("UPDATE `user` SET `userName`=:userName,`email`=:email WHERE idUser = :idUser");
        $req->execute($commentArray);
    }
    public function getUser(int $user = null): ?string
    {
        return null;
    }
    public function getIdUser(): ?int
    {
        return null;
    }
    public function getPassword(string $email): ?string
    {
        return null;
    }

    /* CUD */
    public function createUser(array $data): void
    {

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
