<?php
declare(strict_types=1);
namespace App\Model\Repository;

use PDO;
use App\Service\Database;
use App\Model\Entity\User;

final class UserRepository
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database->getPdo();
    }
    /**
     * Create a user with the given parameters
     *
     * @param string $email
     * @param string $pseudo
     * @param string $password
     * @return void
     */
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
    /**
     * Count all user in database
     *
     * @return array
     */
    public function count(): int
    {
        $pdo = $this->database->query("SELECT count(*) FROM user");
        $pdo->execute();
        $execute =$pdo->fetch();
        return (int) $execute[0];
    }
    /**
     * Change the role with the id user
     *
     * @param integer $actived
     * @param string $userType
     * @param integer $idUser
     * @return void
     */
    public function changeRoleUser(int $actived, string $userType, int $idUser): void
    {
        $tabUser = [
            ':actived' => $actived,
            ':userType' => $userType,
            ':idUser' => $idUser,
        ];
        $req = $this->database->prepare("UPDATE `user` SET `activated` = :actived,`userType` = :userType WHERE `user`.`idUser` = :idUser");
        $req->execute($tabUser);
    }
    /**
     * Update a user with the given parameters
     *
     * @param string $email
     * @param string $userName
     * @param integer $idUser
     * @return void
     */
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
    /**
     * Update a password with the given parameters
     *
     * @param string $password
     * @param integer $idUser
     * @return void
     */
    public function updatePassword(string $password, int $idUser): void
    {
        $reqArray = [
            ':passwordUser' => $password,
            ':idUser' => $idUser,
        ];
        $req = $this->database->prepare("UPDATE user SET passwordUser=:passwordUser WHERE idUser = :idUser");
        $req->execute($reqArray);
    }
    /**
     * Get all user for pagination
     *
     * @param integer $pageuser
     * @param integer $perPage
     * @return array|null
     */
    public function findAll(int $pageUser, int $perPage): ?array
    {
        $req = null;
        if (empty($pageUser) && empty($perPage)) {
            return null;
        }
        $req = $this->database->prepare("SELECT * FROM user ORDER BY idUser DESC LIMIT :pageUser, :perPage");
        $req->bindValue(":pageUser", $pageUser, PDO::PARAM_INT);
        $req->bindValue(":perPage", $perPage, PDO::PARAM_INT);
        $req->execute();
        $pdoStatement = $req->fetchAll();
        return $pdoStatement;
    }
    /**
     * Allows to retrieve a user with the id
     *
     * @param integer $idUser
     * @return User|null
     */
    public function findByIdUser(int $idUser): ?User
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
    /**
     * Allows to retrieve a user with the pseudo
     *
     * @param string $pseudo
     * @return User|null
     */
    public function findByPseudo(string $pseudo = null): ?User
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
    /**
     * Allows you to find a user with the email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email = null): ?User
    {
        $req = [
            ':email' => $email
        ];
        $pdo = $this->database->prepare("SELECT * FROM user WHERE email = :email");
        $executeIsOk = $pdo->execute($req);
        $userBdd = $pdo->fetchObject(User::class);
        if ($executeIsOk === false || $userBdd === false) {
            return null;
        }
        return $userBdd;
    }
}
