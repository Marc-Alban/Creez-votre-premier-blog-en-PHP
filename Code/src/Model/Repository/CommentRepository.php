<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Service\Database;

final class CommentRepository
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database->getPdo();
    }
    /**
     * Create a comment with the given parameters
     *
     * @param string $comment
     * @param integer $idUser
     * @param integer $idPost
     * @return void
     */
    public function create(string $comment, int $idUser, int $idPost): void
    {
        $sql = "
        INSERT INTO comment(content, disabled, UserId, PostId, dateCreation)
        VALUES(:content, :disabled, :UserId, :PostId, CURRENT_TIMESTAMP)
        ";
        $commentArray = [
            ':content' => $comment,
            ':disabled' => 1,
            ':UserId' => $idUser,
            ':PostId' => $idPost,
        ];
        $req = $this->database->prepare($sql);
        $req->execute($commentArray);
    }
    /**
     * Allows to retrieve a comment with the id
     *
     * @param integer $postId
     * @return array|null
     */
    public function findByPostId(int $postId): ?array
    {
        $req = [
                ':idPost' => $postId
            ];
        $pdo = $this->database->prepare("SELECT * FROM comment WHERE disabled = 0  AND PostId = :idPost");
        $executeIsOk = $pdo->execute($req);
        if ($executeIsOk === true) {
            $commentBdd = $pdo->fetchAll();
            if ($commentBdd) {
                return $commentBdd;
            }
            return null;
        }
        return null;
    }
    /**
     * Get all comments
     *
     * @return array|null
     */
    public function findAll(): ?array
    {
        $pdo = $this->database->query("SELECT * FROM comment WHERE disabled = 1");
        $executeIsOk = $pdo->execute();
        if ($executeIsOk === true) {
            $commentBdd = $pdo->fetchAll();
            if ($commentBdd) {
                return $commentBdd;
            }
            return null;
        }
        return null;
    }
    /**
     * Return the name of user wher the UserId is passed
     *
     * @param integer $userId
     * @return User|null
     */
    public function findUserNameByUserId(int $userId): ?User
    {
        $req = [
            ':UserId' => $userId
        ];
    $pdo = $this->database->prepare("SELECT userName FROM user WHERE idUser = :UserId");
    $executeIsOk = $pdo->execute($req);
    if ($executeIsOk === false) {
        return null;
    }
    return $pdo->fetchObject(User::class);
    }
    /**
     * Allows you to validate a comment
     *
     * @param integer $idComment
     * @return boolean
     */
    public function valide(int $idComment): bool
    {
        $tab = [
            ':disabled' => 0,
            ':idComment' => $idComment
        ];
        $pdo = $this->database->prepare("UPDATE comment SET disabled = :disabled WHERE idComment = :idComment");
        return $pdo->execute($tab);
    }
    /**
     * Allows you to delete a comment
     *
     * @param integer $idComment
     * @return boolean
     */
    public function delete(int $idComment): bool
    {
        $tab = [
            ':idComment' => $idComment,
        ];
        $pdo = $this->database->prepare("DELETE FROM comment WHERE idComment = :idComment ");
        return $pdo->execute($tab);
    }
}
