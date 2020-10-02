<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Model\Repository\Interfaces\PasswordRepositoryInterface;
use App\Model\Repository\Interfaces\UserRepositoryInterface;

use App\Service\Database;

final class PasswordRepository implements PasswordRepositoryInterface, UserRepositoryInterface
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
        return null;
    }
    public function updatePassBdd(string $pass, int $idUser): void
    {
        $commentArray = [
            ':passwordUser' => $pass,
            ':idUser' => $idUser,
        ];
        $req = $this->db->prepare("UPDATE `user` SET `passwordUser`=:passwordUser WHERE idUser = :idUser");
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
    public function getPassword(string $user): ?string
    {
        $tabPass = [
            ':userName' => $user
        ];
        $pdo = $this->db->prepare("SELECT passwordUser FROM `user` WHERE userName = :userName");
        $executeIsOk = $pdo->execute($tabPass);
        if ($executeIsOk === true) {
            $passwordBdd = $pdo->fetchObject(User::class);
            if ($passwordBdd) {
                $pass = $passwordBdd->getPasswordUser();
                return $pass;
            } elseif ($passwordBdd === false) {
                return null;
            }
            return null;
        } elseif ($executeIsOk === false) {
            return null;
        }
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
