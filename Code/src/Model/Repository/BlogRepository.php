<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\BlogRepositoryInterface;
final class BlogRepository implements BlogRepositoryInterface
{
    private $db;
    private $post = [];

    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }
    

    public function readAllPost(int $page, int $perPage): array
    {
    
        $pdoStatement = $this->db->query("SELECT * FROM post WHERE statuPost = 1 ORDER BY idPost DESC LIMIT $page,$perPage");

        if($pdoStatement === false){
            header('Location: index.php?page=blog&pp=1');
            exit();
        }
        
        $this->post = $pdoStatement->fetchAll();

        if ($this->post === false) {
            header('Location: index.php?page=blog&pp=1');
            exit();
        };
        
        return $this->post;
    }

    public function count(): ?string
    {

        $this->pdoStatement = $this->db->query("SELECT count(*) AS total FROM post WHERE statuPost = 1 ");

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
