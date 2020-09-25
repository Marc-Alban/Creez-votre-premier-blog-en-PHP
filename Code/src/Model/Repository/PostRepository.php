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

    public function joinUserPost(int $idPost): ?User
    {
        $req = [
            ':idPost' => $idPost,
        ];
        $pdo = $this->db->prepare("SELECT * FROM user INNER JOIN post ON user.idUser = post.UserID && idPost = :idPost");
        $executeIsOk = $pdo->execute($req);
        if ($executeIsOk === true) {
            $user = $pdo->fetchObject(User::class) ;
            if ($user) {
                return $user;
            } elseif ($user === false) {
                return null;
            }
            return $user;
        } elseif ($executeIsOk === false) {
            return null;
        }
        return null;
    }

    public function getEmail(User $user): ?User
    {
        return null;
    }

    public function getUser(User $user): ?User
    {
        return null;
    }

    public function getPassword(User $user): ?User
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

    public function getComment(Comment $comment): ?Comment
    {
        return null;
    }
    public function createComment(string $idUser, string $comment, int $idPost): void
    {
        $sql = "
        INSERT INTO comment(content, disabled, UserId, PostId, dateCreation)
        VALUES(:content, :disabled, :UserId, :PostId, :NOW())
        ";
        $commentArray = [
            ':content' => $comment,
            ':disabled' => 1,
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
