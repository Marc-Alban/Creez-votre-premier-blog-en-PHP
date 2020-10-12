<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Service\Database;

final class UserRepository
{
    private $db;
    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
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
    public function createUser(array $data): void
    {
        $tabUser = [
            ':userName' => $data['post']['userName'],
            ':email' => $data['post']['email'],
            ':passwordUser' => password_hash($data['post']['password'], PASSWORD_BCRYPT),
        ];
        $req = $this->db->prepare("INSERT INTO user (userName, email, passwordUser) VALUES (:userName, :email, :passwordUser)");
        $req->execute($tabUser);
    }
    public function updateUserBdd(int $idUser, string $email, string $userName): void
    {
        $commentArray = [
            ':idUser' => $idUser,
            ':email' => $email,
            ':userName' => $userName,
        ];
        $req = $this->db->prepare("UPDATE `user` SET `userName`=:userName,`email`=:email WHERE idUser = :idUser");
        $req->execute($commentArray);
    }
    public function getEmailBdd(string $email): ?string
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
    public function getUser(int $user = null): ?string
    {
        if ($user !== null) {
            $req = [
                ':idUser' => $user
            ];
            $pdo = $this->db->prepare("SELECT userName FROM user WHERE idUser = :idUser");
            $executeIsOk = $pdo->execute($req);
        } elseif ($user === null) {
            $pdo = $this->db->query("SELECT userName FROM user");
            $executeIsOk = $pdo->execute();
        }
        if ($executeIsOk === true) {
            $userBdd = $pdo->fetchObject(User::class);
            if ($userBdd) {
                $user = $userBdd->getUserName();
                return $user;
            } elseif ($userBdd === false) {
                return null;
            }
            return $userBdd;
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }
    public function getIdUser(): ?int
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
    public function getPassword(string $user = null, string $email = null): ?string
    {
        if (isset($user)) {
            $tabPass = [
                ':userName' => $user
            ];
            $pdo = $this->db->prepare("SELECT passwordUser FROM `user` WHERE userName = :userName");
            $executeIsOk = $pdo->execute($tabPass);
        } elseif (isset($email)) {
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
            } elseif ($passwordBdd === false) {
                return null;
            }
            return null;
        } elseif ($executeIsOk === false) {
            return null;
        }
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
}
