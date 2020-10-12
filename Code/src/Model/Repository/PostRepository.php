<?php
declare(strict_types=1);
namespace App\Model\Repository;
use App\Service\Database;
use App\Model\Entity\Post;
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
