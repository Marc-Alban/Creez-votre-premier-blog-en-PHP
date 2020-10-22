<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Service\Database;
use App\Service\Http\Parameter;

final class UserRepository
{
    private $db;
    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }
    public function create(string $email, string $pseudo, string $password): void
    {
        $tabUser = [
            ':userName' => $pseudo,
            ':email' => $email,
            ':passwordUser' => password_hash($password, PASSWORD_BCRYPT),
        ];
        $req = $this->db->prepare("INSERT INTO user (userName, email, passwordUser) VALUES (:userName, :email, :passwordUser)");
        $req->execute($tabUser);
    }
    public function update(int $idUser, string $email, string $userName): void
    {
        $commentArray = [
            ':idUser' => $idUser,
            ':email' => $email,
            ':userName' => $userName,
        ];
        $req = $this->db->prepare("UPDATE `user` SET `userName`=:userName,`email`=:email WHERE idUser = :idUser");
        $req->execute($commentArray);
    }
    public function updatePassword(string $pass, int $idUser): void
    {
        $commentArray = [
            ':passwordUser' => $pass,
            ':idUser' => $idUser,
        ];
        $req = $this->db->prepare("UPDATE `user` SET `passwordUser`=:passwordUser WHERE id = :id");
        $req->execute($commentArray);
    }
    public function findById(int $idUser): ?User
    {
        $req = [
                ':idUser' => $idUser
            ];
        $pdo = $this->db->prepare("SELECT * FROM user WHERE idUser = :idUser");
        $executeIsOk = $pdo->execute($req);
        if ($executeIsOk === true) {
            $userBdd = $pdo->fetchObject(User::class);
            return $userBdd;
        }
        return null;
    }
    public function findByName(string $pseudo): ?User
    {
        $tabUser = [
            ':userName' => $pseudo
        ];
        $pdo = $this->db->prepare("SELECT * FROM user WHERE userName = :userName");
        $executeIsOk = $pdo->execute($tabUser);
        $userBdd = $pdo->fetchObject(User::class);
        if ($executeIsOk === true && $userBdd !== false) {
            return $userBdd;
        }
        return null;
    }
    public function findByEmail(string $email): ?User
    {
        $tabEmail = [
            ':email' => $email
        ];
        $pdo = $this->db->prepare("SELECT * FROM user WHERE email = :email");
        $executeIsOk = $pdo->execute($tabEmail);
        $mailBdd = $pdo->fetchObject(User::class);
        if ($executeIsOk === true && $mailBdd !== false) {
            return $mailBdd;
        }
        return null;
    }
    public function findPasswordByUserOrEmail(string $user = null, string $email = null): ?string
    {
        $executeIsOk = null;
        $pdo = null;
        if ($user !== null && $email === null) {
            $tabPass = [
                ':userName' => $user
            ];
            $pdo = $this->db->prepare("SELECT passwordUser FROM `user` WHERE userName = :userName");
            $executeIsOk = $pdo->execute($tabPass);
        } elseif ($user === null && $email !== null) {
            $tabPass = [
                ':email' => $email
            ];
            $pdo = $this->db->prepare("SELECT passwordUser FROM user WHERE email = :email");
            $executeIsOk = $pdo->execute($tabPass);
        }
        if ($executeIsOk === true) {
            $passwordBdd = $pdo->fetchObject(User::class);
            if ($passwordBdd) {
                $pass = $passwordBdd->getPasswordUser();
                return $pass;
            }
            return null;
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }
    public function findAll(): ?User
    {
        $pdo = $this->db->query("SELECT * FROM user");
        $executeIsOk = $pdo->execute();
        if ($executeIsOk === false) {
            return null;
        }
        return $pdo->fetchObject(User::class);
    }
}
