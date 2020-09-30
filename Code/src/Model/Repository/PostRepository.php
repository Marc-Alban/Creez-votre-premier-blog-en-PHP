<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\Post;
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

    public function getEmailBdd(string $email): ?string
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

    public function getComment(int $postId): ?Comment
    {
        $req = [
            ':idPost' => $postId
        ];
        $pdo = $this->db->prepare("SELECT content, userComment FROM comment WHERE disabled = 0 AND signalComment = 0 AND PostId = :idPost");
        $executeIsOk = $pdo->execute($req);
        if ($executeIsOk === true) {
            $commentBdd = $pdo->fetchObject(Comment::class);
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
            ':signalComment' => 1,
            ':UserComment' => $UserComment,
            ':UserId' => $idUser,
            ':PostId' => $idPost,
        ];
        $req = $this->db->prepare($sql);
        $req->execute($commentArray);
    }
    public function deleteComment(Comment $comment): bool
    {
        return false;
    }

}
