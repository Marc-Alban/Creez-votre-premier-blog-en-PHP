<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\Comment;
use App\Service\Database;
use PDO;

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
     * Allows to retrieve a comment with the idPost
     *
     * @param integer $postId
     * @return array|null
     */
    public function findByPostId(int $postId): ?array
    {
        $req = [
                ':idPost' => $postId
            ];
        $pdoStat = $this->database->prepare("SELECT * FROM comment INNER JOIN user ON comment.UserId = user.idUser WHERE  disabled = 0 AND comment.UserId = user.idUser AND PostId = :idPost");
        $executeIsOk =$pdoStat->execute($req);
        if ($executeIsOk === false) {
            return null;
        }
        $comments = $pdoStat->fetchAll();
        return $comments;
    }
    /**
     * Get all comments for pagination
     *
     * @param integer $pageComment
     * @param integer $perPage
     * @return array|null
     */
    public function findAll(int $pageComment, int $perPage): ?array
    {
        $req = null;
        if (empty($pageComment) && empty($perPage)) {
            return null;
        }
        $req = $this->database->prepare("SELECT * FROM comment ORDER BY idComment DESC LIMIT :pageComment, :perPage");
        $req->bindValue(":pageComment", $pageComment, PDO::PARAM_INT);
        $req->bindValue(":perPage", $perPage, PDO::PARAM_INT);
        $req->execute();
        $pdoStatement = $req->fetchAll();
        return $pdoStatement;
    }
    /**
     * Count all comment and return a integer
     *
     * @param integer $disabled
     * @return integer|null
     */
    public function count(int $disabled): ?int
    {
        $pdo = null;
        if ($disabled === 0) {
            $pdo = $this->database->query("SELECT count(*) FROM comment WHERE disabled = 0");
        } elseif ($disabled === 1) {
            $pdo = $this->database->query("SELECT count(*) FROM comment WHERE disabled = 1");
        }
        $pdo->execute();
        if ($pdo->execute() === true) {
            $nbComment = $pdo->fetch();
            return (int) $nbComment[0];
        }
        return null;
    }
    /**
     * Count all comment in database
     *
     * @return int
     */
    public function total(): int
    {
        $pdo = $this->database->query("SELECT count(*) FROM comment");
        $pdo->execute();
        $execute =$pdo->fetch();
        return (int) $execute[0];
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
