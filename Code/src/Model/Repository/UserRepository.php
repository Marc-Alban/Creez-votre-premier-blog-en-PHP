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
    public function create(Parameter $dataPost): void
    {
        // $tabUser = [
        //     ':userName' => $data['post']['userName'],
        //     ':email' => $data['post']['email'],
        //     ':passwordUser' => password_hash($data['post']['password'], PASSWORD_BCRYPT),
        // ];
        // $req = $this->db->prepare("INSERT INTO user (userName, email, passwordUser) VALUES (:userName, :email, :passwordUser)");
        // $req->execute($tabUser);
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
        $req = $this->db->prepare("UPDATE `user` SET `passwordUser`=:passwordUser WHERE idUser = :idUser");
        $req->execute($commentArray);
    }
    public function findId(): ?int
    {
        $pdo = $this->db->query("SELECT idUser FROM user");
        $executeIsOk = $pdo->execute();
        if ($executeIsOk === true) {
            $idUserBdd = $pdo->fetchObject(User::class);
            if ($idUserBdd) {
                $id = $idUserBdd->getIdUser();
                return $id;
            } elseif ($idUserBdd === false) {
                return null;
            }
            return null;
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }
    public function findById(int $idUser = null): ?string
    {
        $executeIsOk = null;
        $pdo = null;
        if ($idUser !== null) {
            $req = [
                ':idUser' => $idUser
            ];
            $pdo = $this->db->prepare("SELECT userName FROM user WHERE idUser = :idUser");
            $executeIsOk = $pdo->execute($req);
        } elseif ($idUser === null) {
            $pdo = $this->db->query("SELECT userName FROM user");
            $executeIsOk = $pdo->execute();
        }
        if ($executeIsOk === true) {
            $userBdd = $pdo->fetchObject(User::class);
            if ($userBdd) {
                $idUser = $userBdd->getUserName();
                return $idUser;
            } elseif ($userBdd === false) {
                return null;
            }
            return $userBdd;
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }
    public function findByEmail(string $email): ?string
    {
        $tabEmail = [
            ':email' => $email
        ];
        $pdo = $this->db->prepare("SELECT email FROM user WHERE email = :email");
        $executeIsOk = $pdo->execute($tabEmail);
        if ($executeIsOk === true) {
            $mailBdd = $pdo->fetchObject(User::class);
            if ($mailBdd) {
                $mail = $mailBdd->getEmail();
                return $mail;
            } elseif ($mailBdd === false) {
                return null;
            }
            return null;
        } elseif ($executeIsOk === false) {
            return null;
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
