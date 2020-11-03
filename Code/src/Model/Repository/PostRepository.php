<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Entity\Post;
use App\Service\Database;
use PDO;

final class PostRepository
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database->getPdo();
    }
    public function create(array $dataForm): ?array
    {
        $idPostMax = $this->database->query('SELECT MAX(idPost) FROM post ORDER BY idPost');
        $response = $idPostMax->fetch();
        $idPost = $response['MAX(idPost)'] + 1;
        $req = [
            ':title'=>$dataForm['title'],
            ':description'=>$dataForm['description'],
            ':chapo'=>$dataForm['chapo'],
            ':imagePost'=>$idPost.".".$dataForm['extention'],
            ':statuPost'=>1,
            ':UserId'=>$dataForm['idUser']
        ];
        $pdoStatement = $this->database->prepare('INSERT INTO post(title,description,chapo,imagePost, datePost, statuPost, UserId) VALUES (:title,:description, :chapo,:imagePost, NOW(), :statuPost,:UserId)');
        $pdoStatement->execute($req);
        move_uploaded_file($dataForm['tmpName'], "images/post/" . $idPost . '.' . $dataForm['extention']);
        return null;
    }
    public function findIdUser(string $email): int
    {
        $req = [
            ':email' => $email
        ];
        $pdo = $this->database->prepare("SELECT UserId FROM post WHERE email = :email");
        $executeIsOk = $pdo->execute($req);
        if ($executeIsOk === false) {
            return null;
        }
        return $pdo->fetch();
    }
    public function findById(int $idPost): ?Post
    {
        $req = [
            ':idPost' => $idPost
        ];
        $pdo = $this->database->prepare("SELECT * FROM post WHERE idPost= :idPost");
        $executeIsOk = $pdo->execute($req);
        if ($executeIsOk === false) {
            return null;
        }
        return $pdo->fetchObject(Post::class);
    }
    public function findAll(int $pagePost, int $perPage): ?array
    {
        $req = $this->database->prepare("SELECT * FROM post WHERE statuPost = 1 ORDER BY idPost DESC LIMIT :pagePost, :perPage");
        $req->bindValue(":pagePost", $pagePost, PDO::PARAM_INT);
        $req->bindValue(":perPage", $perPage, PDO::PARAM_INT);
        $req->execute();
        $pdoStatement = $req->fetchAll(PDO::FETCH_CLASS);
        if (empty($pdoStatement)) {
            return null;
        }
        return $pdoStatement;
    }
    public function count(): string
    {
        $total = null;
        $pdoStatement = $this->database->query("SELECT count(*) AS total FROM post WHERE statuPost = 1 ");
        if ($pdoStatement === false) {
            $total = "1";
        } elseif ($pdoStatement !== false) {
            $req = $pdoStatement->fetch();
            $total = $req['total'];
        }
        return $total;
    }
}
