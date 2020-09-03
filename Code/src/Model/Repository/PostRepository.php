<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\PostRepositoryInterface;
final class PostRepository implements PostRepositoryInterface
{
    private $db;

    public function __construct(Database $instanceDb)
    {
        $this->db = $instanceDb->getPdo();
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

    public function findByAll(): ?array
    {
        return null;
    }

    public function create(Post $post) : bool
    {
        return false;
    }

    public function update(Post $post) : bool
    {
        return false;
    }

    public function delete(Post $post) : bool
    {
        return false;
    }
}
