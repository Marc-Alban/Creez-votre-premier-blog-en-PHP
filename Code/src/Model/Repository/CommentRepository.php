<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Service\Database;

final class CommentRepository
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }
    public function create(string $comment, string $UserComment, int $idUser, int $idPost): void
    {
        $sql = "
        INSERT INTO comment(content, disabled, signalComment,UserComment, UserId, PostId, dateCreation)
        VALUES(:content, :disabled, :signalComment,:UserComment, :UserId, :PostId, CURRENT_TIMESTAMP)
        ";
        $commentArray = [
            ':content' => $comment,
            ':disabled' => 1,
            ':signalComment' => 0,
            ':UserComment' => $UserComment,
            ':UserId' => $idUser,
            ':PostId' => $idPost,
        ];
        $req = $this->db->prepare($sql);
        $req->execute($commentArray);
    }
    public function findById(int $postId): ?array
    {
        $req = [
                ':idPost' => $postId
            ];
        $pdo = $this->db->prepare("SELECT * FROM comment WHERE disabled = 0  AND PostId = :idPost");
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
    public function findAll(): ?array
    {
        $pdo = $this->db->query("SELECT * FROM comment");
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
    public function valid(int $idComment, int $signal = 0): bool
    {
        if ($signal === 0) {
            $tab = [
                ':disabled' => 0,
                ':idComment' => $idComment
            ];
            $pdo = $this->db->prepare("UPDATE comment SET disabled = :disabled WHERE idComment = :idComment");
            return $pdo->execute($tab);
        }
        $tab = [
            ':signalComment' => 0,
            ':idComment' => $idComment
        ];
        $pdo = $this->db->prepare("UPDATE comment SET signalComment = :signalComment WHERE idComment = :idComment");
        return $pdo->execute($tab);
    }
    public function delete(int $idComment): bool
    {
        $tab = [
            ':idComment' => $idComment,
        ];
        $pdo = $this->db->prepare("DELETE FROM comment WHERE idComment = :idComment ");
        return $pdo->execute($tab);
    }
    public function signal(int $idComment): bool
    {
        $commentArray = [
            ':signalComment' => 1,
            ':idComment' => $idComment,
        ];
        $req = $this->db->prepare("UPDATE `comment` SET `signalComment`=:signalComment  WHERE  idComment = :idComment");
        return $req->execute($commentArray);
    }
}
