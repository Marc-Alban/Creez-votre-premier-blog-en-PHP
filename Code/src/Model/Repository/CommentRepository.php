<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Service\Database;


final class CommentRepository
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database->getPdo();
    }
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
    public function findById(int $postId): ?array
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
    public function findByIdPost(int $idPost)
    {
        $tab = [
            'idPost'=>$idPost
        ];
        $pdo = $this->database->prepare("SELECT UserId FROM comment WHERE idPost = :idPost");
        $executeIsOk = $pdo->execute($tab);
        if ($executeIsOk === true) {
            $commentBdd = $pdo->fetchAll();
            if ($commentBdd) {
                return $commentBdd;
            }
            return null;
        }
        return null;
    }
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
    public function valide(int $idComment): bool
    {
        $tab = [
            ':disabled' => 0,
            ':idComment' => $idComment
        ];
        $pdo = $this->database->prepare("UPDATE comment SET disabled = :disabled WHERE idComment = :idComment");
        return $pdo->execute($tab);
    }
    public function delete(int $idComment): bool
    {
        $tab = [
            ':idComment' => $idComment,
        ];
        $pdo = $this->database->prepare("DELETE FROM comment WHERE idComment = :idComment ");
        return $pdo->execute($tab);
    }
}
