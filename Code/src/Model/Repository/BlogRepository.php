<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\BlogRepositoryInterface;
final class BlogRepository implements BlogRepositoryInterface
{
    private $db;
    private $post;

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

    /************************************last Post************************************************* */
    /**
     * Récupère le dernier Post
     *
     * @return bool|Post|null
     * false si l'objet n'a pu être inséré, objet Post si une
     * correspondance est trouvé, NULL s'il n'y a aucune correspondance
     */
    public function lastPost(): ?Object
    {

        $this->pdoStatement = $this->db->query('SELECT * FROM post WHERE lastPost = 1 AND statuPost = 1 ORDER BY dateCreation DESC LIMIT 1');
        //execution de la requête
        $executeIsOk = $this->pdoStatement->execute();
        if ($executeIsOk) {
            return $this->post = $this->pdoStatement->fetchObject(Post::class);
        } else if (!$executeIsOk) {
            return null;
        }
    }
/************************************End last Post************************************************* */

    public function readAllPost(int $page, int $perPage, string $side): array
    {
        if (isset($side) && !empty($side) && $side !== null) {
            if ($side === "readAll") {
                $pdoStatement = $this->db->query("SELECT * FROM post ORDER BY dateCreation LIMIT $page,$perPage");
            } else if ($side === "readAllNoOne") {
                $pdoStatement = $this->db->query("SELECT * FROM post WHERE lastPost != 1 AND statuPost = 1 ORDER BY idPost LIMIT $page,$perPage");
            }
        }
        $this->post = [];
        $post = 1;

        if($pdoStatement === false){
            header('Location: index.php?page=blog');
            exit();
        }
        
        if ($this->post === false) {
            header('Location: index.php?page=blog');
            exit();
        };
        
        $this->post[] = $pdoStatement->fetchObject(Post::class);
        return $this->post;
    }

    public function count(string $side): ?string
    {
        if (isset($side) && !empty($side) && $side !== null) {
            if ($side === 'front') {
                $this->pdoStatement = $this->db->query("SELECT count(*) AS total FROM post WHERE lastPost != 1 AND statuPost = 1 ");

            } elseif ($side === 'back') {

                $this->pdoStatement = $this->db->query("SELECT count(*) AS total FROM post WHERE statuPost = 1 ");
            }
        }

        if($this->pdoStatement === false){
            return null;
        }else if(!$this->pdoStatement === false){
            $req = $this->pdoStatement->fetch();
            if ($req) {
                $total = $req['total'];
                return $total;
            }
            return null;
        }



    }

}
