<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Model\Repository\Interfaces\AddPostRepositoryInterface;
use App\Service\Database;
use App\Model\Entity\Post;

final class AddPostRepository implements AddPostRepositoryInterface
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
}
