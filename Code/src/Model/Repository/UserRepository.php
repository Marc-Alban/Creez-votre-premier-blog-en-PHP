<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Service\Database;
use App\Service\Http\Parameter;

final class UserRepository
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database->getPdo();
    }
    public function create(string $email, string $pseudo, string $password): void
    {
        $tabUser = [
            ':userName' => $pseudo,
            ':email' => $email,
            ':passwordUser' => password_hash($password, PASSWORD_BCRYPT),
        ];
        $req = $this->database->prepare("INSERT INTO user (userName, email, passwordUser) VALUES (:userName, :email, :passwordUser)");
        $req->execute($tabUser);
    }
    public function update(string $email, string $userName, int $idUser): void
    {
        $reqArray = [
            ':email' => $email,
            ':userName' => $userName,
            ':idUser' => $idUser
        ];
        $req = $this->database->prepare("UPDATE user SET userName=:userName,email=:email WHERE idUser = :idUser");
        $req->execute($reqArray);
    }
    public function updatePassword(string $password, int $idUser): void
    {
        $reqArray = [
            ':passwordUser' => $password,
            ':idUser' => $idUser,
        ];
        $req = $this->database->prepare("UPDATE user SET passwordUser=:passwordUser WHERE idUser = :idUser");
        $req->execute($reqArray);
    }
    public function findById(int $idUser): ?User
    {
        $req = [
                ':idUser' => $idUser
            ];
        $pdo = $this->database->prepare("SELECT * FROM user WHERE idUser = :idUser");
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
        $pdo = $this->database->prepare("SELECT * FROM user WHERE userName = :userName");
        $executeIsOk = $pdo->execute($tabUser);
        $userBdd = $pdo->fetchObject(User::class);
        if ($executeIsOk === true && $userBdd !== false) {
            return $userBdd;
        }
        return null;
    }
    public function findByEmail(string $email = null): ?User
    {
        $tabEmail = [
            ':email' => $email
        ];
        $pdo = $this->database->prepare("SELECT * FROM user WHERE email = :email");
        $pdo->execute($tabEmail);
        $mailBdd = $pdo->fetchObject(User::class);
        if ($mailBdd !== false) {
            return $mailBdd;
        }
        return null;
    }
}
