<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\PostRepositoryInterface;
use App\Service\Database;
final class PostRepository implements PostRepositoryInterface
{
    private $db;

    public function __construct(Database $instanceDb)
    {
        $this->db = $instanceDb->getPdo();
    }
    
    public function findById(int $id): ?Post
    {
        $e = [
            ':id' => $id,
        ];
        $pdo = $this->db->prepare("SELECT * FROM post WHERE idPost=:id");
        $executeIsOk = $pdo->execute($e);
        if ($executeIsOk === true) {
            $idBdd = $pdo->fetchObject();
            if ($idBdd) {

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
