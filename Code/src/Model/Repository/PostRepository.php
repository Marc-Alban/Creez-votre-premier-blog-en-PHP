<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\Post;
use App\Service\Database;

final class PostRepository
{
    private $db;
    public function __construct(Database $db)
    {
        $this->db = $db->getPdo();
    }
    public function createPost(array $dataForm): ?array
    {
        $idPostMax = $this->db->query('SELECT MAX(idPost) FROM post ORDER BY idPost');
        $response = $idPostMax->fetch();
        $id = $response['MAX(idPost)'] + 1;
        $req = [
            ':title'=>$dataForm['title'],
            ':description'=>$dataForm['description'],
            ':chapo'=>$dataForm['chapo'],
            ':imagePost'=>$id.".".$dataForm['extention'],
            ':statuPost'=>1,
            ':UserId'=>$dataForm['idUser']
        ];
        $pdoStatement = $this->db->prepare('INSERT INTO post(title,description,chapo,imagePost, datePost, statuPost, UserId) VALUES (:title,:description, :chapo,:imagePost, NOW(), :statuPost,:UserId)');
        $pdoStatement->execute($req);
        move_uploaded_file($dataForm['tmpName'], "images/post/" . $id . '.' . $dataForm['extention']);
        return null;
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
    public function readAllPost(int $page, int $perPage): ?array
    {
        $pdo = $this->db->query("SELECT * FROM post WHERE statuPost = 1 ORDER BY idPost DESC LIMIT $page,$perPage ");
        $postAll = $pdo->fetchAll();
        if ($postAll === false) {
            return null;
        }
        return $postAll;
    }
    public function count(): ?string
    {
        $this->pdoStatement = $this->db->query("SELECT count(*) AS total FROM post WHERE statuPost = 1 ");
        if ($this->pdoStatement === false) {
            return null;
        }
        $req = $this->pdoStatement->fetch();
        $total = $req['total'];
        return $total;
    }
}
