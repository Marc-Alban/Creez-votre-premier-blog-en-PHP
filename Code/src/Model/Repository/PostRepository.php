<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Post;
use App\Model\Repository\Interfaces\PostRepositoryInterface;
use App\Service\Database;

final class PostRepository implements PostRepositoryInterface
{
    private $pdo;
    private $pdoStatement;

    public function __construct()
    {
        $this->pdo = Database::getPdo();
    }
    
    public function findById(int $id): ?Post
    {
        $e = [
            ':id' => $id,
        ];
        $this->pdoStatement = $this->pdo->prepare("SELECT * FROM post WHERE idPost=:id");
        $executeIsOk = $this->pdoStatement->execute($e);
        if ($executeIsOk === true) {
            $idBdd = $this->pdoStatement->fetch();
            if ($idBdd) {
                $data = [
                    'idPost'=>(int) $idBdd['idPost'],
                    'title' => $idBdd['title'],
                    'description' => $idBdd['description'],
                    'label' => $idBdd['label'],
                    'imagePost' => $idBdd['imagePost'],
                    'categorie' => $idBdd['categorie'],
                    'dateCreation' => $idBdd['dateCreation'],
                    'dateUpdate' => (string) $idBdd['dateUpdate'],
                    'statuPost' => $idBdd['statuPost'],
                    'UserId' => (int) $idBdd['UserId']
                ];
                return new Post(
                    $data['idPost'],
                    $data['title'],
                    $data['description'],
                    $data['label'],
                    $data['imagePost'],
                    $data['categorie'],
                    $data['dateCreation'],
                    $data['dateUpdate'],
                    $data['statuPost'],
                    $data['UserId'],
                );
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
