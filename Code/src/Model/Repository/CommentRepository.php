<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Model\Entity\Comment;
use App\Model\Repository\Interfaces\CommentRepositoryInterface;

use App\Service\Database;

final class CommentRepository implements CommentRepositoryInterface
{

    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }

    public function validedCommentBdd(int $idComment, int $signal = null): ?string
    {
        if(isset($idComment) && $signal === 0){
            $tab = [
                ':disabled' => 0,
                ':idComment' => $idComment
            ];
            $pdo = $this->db->prepare("UPDATE comment SET disabled = :disabled WHERE idComment = :idComment");
            $pdo->execute($tab);
        }else if(isset($idComment) && $signal === 1){
            $tab = [
                ':signalComment' => 0,
                ':idComment' => $idComment
            ];
            $pdo = $this->db->prepare("UPDATE comment SET signalComment = :signalComment WHERE idComment = :idComment");
            $pdo->execute($tab);
        }
        return null;
    }
    public function deletedCommentBdd(int $idComment): ?string
    {
        if(isset($idComment)){
            $tab = [
                ':idComment' => $idComment,
            ];
            $pdo = $this->db->prepare("DELETE FROM comment WHERE idComment = :idComment ");
            $pdo->execute($tab);
        }
        return null;
    }
    public function getAllCommentBdd(): ?array
    {
        $pdo = $this->db->query("SELECT * FROM comment");
        $executeIsOk = $pdo->execute();
        if ($executeIsOk === true) {
            $commentBdd = $pdo->fetchAll();
            if ($commentBdd) {
                return $commentBdd;
            } elseif ($commentBdd === false) {
                return null;
            }
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }
    public function getComment(int $postId): ?array
    {
        return null;
    }
    public function createComment(string $comment, string $UserComment, int $idUser, int $idPost): void
    {
        
    }
    public function signalCommentBdd(int $idComment): void
    {

    }
}
