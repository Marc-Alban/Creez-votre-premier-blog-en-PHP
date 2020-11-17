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
    /**
     * Return the id of the max idPost in database
     *
     * @return integer
     */
    private function idParams(string $actionPost = null): int
    {
        $idPostMax = $this->database->query('SELECT MAX(idPost) FROM post ORDER BY idPost');
        $response = $idPostMax->fetch();
        if ($actionPost === 'create') {
            return (int) $response['MAX(idPost)'] + 1;
        }
        return (int) $response['MAX(idPost)'];
    }
    /**
     * Array, who have the content of the form to add or update a post
     *
     * @param array $dataForm
     * @return array
     */
    private function params(array $dataForm, string $idImage): array
    {
        return [
            'idPost'=>$dataForm['idPost'],
            ':title'=>$dataForm['title'],
            ':description'=>$dataForm['description'],
            ':chapo'=>$dataForm['chapo'],
            ':imagePost'=> $idImage,
            ':statuPost'=>$dataForm['statuPost'],
            ':UserId'=>$dataForm['userId'],
        ];
    }
    /**
     * Delete a post whit idPost
     *
     * @param integer $idPost
     * @return void
     */
    public function delete(int $idPost): void
    {
        $req = [
            'idPost'=>$idPost
        ];
        $pdoStatement = $this->database->prepare("DELETE FROM post WHERE idPost = :idPost");
        $pdoStatement->execute($req);
    }
    /**
     * Create a post with the given parameters
     *
     * @param array $dataForm
     * @return void
     */
    public function create(array $dataForm): void
    {
        $actionPost = $this->idParams('create').".".$dataForm['extention'];
        move_uploaded_file($dataForm['tmpName'], "images/post/" . $this->idParams('create') . '.' . $dataForm['extention']);
        $pdoStatement = $this->database->prepare('INSERT INTO post(idPost,title,description,chapo,imagePost, datePost, statuPost, UserId) VALUES (:idPost,:title,:description, :chapo,:imagePost, NOW(), :statuPost,:UserId)');
        $pdoStatement->execute($this->params($dataForm, $actionPost));
    }
    /**
     * Update a post with the given parameters
     *
     * @param array $dataForm
     * @return void
     */
    public function update(array $dataForm, int $idPost): void
    {
        $dataForm['idPost'] = $idPost;
        $actionPost = $this->idParams().".".$dataForm['extention'];
        move_uploaded_file($dataForm['tmpName'], "images/post/" . $this->idParams() . '.' . $dataForm['extention']);
        $pdoStatement = $this->database->prepare('UPDATE post SET title=:title,description=:description,chapo=:chapo,imagePost=:imagePost,datePost=NOW(),statuPost=:statuPost,UserId=:UserId WHERE idPost = :idPost');
        $pdoStatement->execute($this->params($dataForm, $actionPost));
    }
    /**
     * Allows you to find a post with the idPost
     *
     * @param integer $idPost
     * @return Post|null
     */
    public function findByIdPost(int $idPost): ?Post
    {
        $req = [
            ':idPost' => $idPost
        ];
        $pdo = $this->database->prepare("SELECT * FROM post WHERE idPost= :idPost");
        $executeIsOk = $pdo->execute($req);
        $entity = $pdo->fetchObject(Post::class);
        if ($executeIsOk === false || $entity === false) {
            return null;
        }
        return $entity;
    }
    /**
     * Get All id post
     *
     * @return array|null
     */
    public function findIdPost(): ?array
    {
        $pdo = $this->database->query("SELECT idPost FROM post");
        $req = $pdo->fetchAll();
        if ($req) {
            return $req;
        }
        return null;
    }
    /**
     * Get all posts
     *
     * @param integer $pagePost
     * @param integer $perPage
     * @return array|null
     */
    public function findAll(int $pagePost, int $perPage): ?array
    {
        $req = null;
        if (empty($pagePost) && empty($perPage)) {
            return null;
        }
        $req = $this->database->prepare("SELECT * FROM post WHERE statuPost = 1 ORDER BY idPost DESC LIMIT :pagePost, :perPage");
        $req->bindValue(":pagePost", $pagePost, PDO::PARAM_INT);
        $req->bindValue(":perPage", $perPage, PDO::PARAM_INT);
        $req->execute();
        $pdoStatement = $req->fetchAll(PDO::FETCH_CLASS);
        return $pdoStatement;
    }
    /**
     * Count the number of posts
     *
     * @return string
     */
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
