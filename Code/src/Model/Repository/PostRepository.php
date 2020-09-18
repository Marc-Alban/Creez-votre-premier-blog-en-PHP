<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\Post;
use App\Model\Entity\User;
use App\Model\Repository\Interfaces\PostRepositoryInterface;
use App\Model\Repository\Interfaces\UserRepositoryInterface;
final class PostRepository implements PostRepositoryInterface, UserRepositoryInterface
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }
    
    public function findById(int $id): ?Post
    {
        $pdo = $this->db->prepare("SELECT * FROM post WHERE idPost=?");
        $executeIsOk = $pdo->execute([$id]);
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
        $pdo = $this->db->prepare("SELECT * FROM user INNER JOIN post ON user.idUser = post.UserID && idPost = ?");
        $executeIsOk = $pdo->execute([$idPost]);
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

    public function createPost(Post $post) : bool
    {
        return false;
    }

    public function updatePost(Post $post) : bool
    {
        return false;
    }

    public function deletePost(Post $post) : bool
    {
        return false;
    }

    public function createUser(User $user) : bool
    {
        return false;
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
