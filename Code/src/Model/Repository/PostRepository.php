<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\Post;
use PDO;
use App\Model\Entity\User;
use App\Model\Entity\Comment;
use App\Model\Repository\Interfaces\PostRepositoryInterface;
use App\Model\Repository\Interfaces\UserRepositoryInterface;
use App\Model\Repository\Interfaces\CommentRepositoryInterface;

final class PostRepository implements PostRepositoryInterface, UserRepositoryInterface, CommentRepositoryInterface
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }
    
    public function findById(int $id): ?Post
    {
        $req = [
            ':idPost' => $id
        ];
        $pdo = $this->db->prepare("SELECT * FROM post WHERE idPost= :idPost");
        $executeIsOk = $pdo->execute($req);
        if ($executeIsOk === true) {
            $idBdd = $pdo->fetchObject(Post::class) ;
            if ($idBdd) {
                return $idBdd;
            } elseif ($idBdd === false) {
                return null;
            }
            return $idBdd;
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }

    public function signalCommentBdd(int $idComment): void
    {

        $commentArray = [
            ':signalComment' => 1,
            ':idComment' => $idComment,
        ];
        $req = $this->db->prepare("UPDATE `comment` SET `signalComment`=:signalComment  WHERE  idComment = :idComment");
        $req->execute($commentArray);
        
    }

    public function getComment(int $postId ): ?array
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
            } elseif ($commentBdd === false) {
                return null;
            }
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }


    public function createComment(string $comment, string $UserComment ,int $idUser, int $idPost): void
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
    public function validedCommentBdd(int $idComment, int $signal = 0): bool
    {
        return false;
    }
    public function deletedCommentBdd(int $idComment): bool
    {
        return false;
    }
    public function getAllCommentBdd(): ?array
    {
        return null;
    }
    public function getEmailBdd(string $email): ?string
    {
        return null;
    }

    public function getAllFromUser(): ?User
    {
        return null;
    }

    public function getUser(int $user = null): ?string
    {
        return null;
    }
    
    public function getIdUser(): ?int
    {
        return null;
    }

    public function getPassword(string $email): ?string
    {
        return null;
    }
    public function createPost(Post $post): void
    {

    }

    public function updatePost(Post $post) : bool
    {
        return false;
    }

    public function deletePost(Post $post) : bool
    {
        return false;
    }

    public function createUser(array $data): void
    {

    }

    public function updateUser(User $user) : bool
    {
        return false;
    }

    public function deleteUser(User $user) : bool
    {
        return false;
    }

}
