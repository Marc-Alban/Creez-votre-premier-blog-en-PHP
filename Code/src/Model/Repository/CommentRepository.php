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

    public function validedCommentBdd(int $idComment, int $signal = 0): bool
    {

        if(isset($idComment) && $signal === 0){
            $tab = [
                ':disabled' => 0,
                ':idComment' => $idComment
            ];
            $pdo = $this->db->prepare("UPDATE comment SET disabled = :disabled WHERE idComment = :idComment");
            $pdo->execute($tab);
            return true;
        }else if(isset($idComment) && $signal === 1){
            $tab = [
                ':signalComment' => 0,
                ':idComment' => $idComment
            ];
            $pdo = $this->db->prepare("UPDATE comment SET signalComment = :signalComment WHERE idComment = :idComment");
            $pdo->execute($tab);
            return true;
        }
        return false;
    }
    public function deletedCommentBdd(int $idComment): bool
    {
        if(isset($idComment)){
            $tab = [
                ':idComment' => $idComment,
            ];
            $pdo = $this->db->prepare("DELETE FROM comment WHERE idComment = :idComment ");
            $pdo->execute($tab);
            return true;
        }
        return false;
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

}
