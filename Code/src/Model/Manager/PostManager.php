<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Entity\Post;
use App\Model\Entity\Comment;
use App\Model\Repository\PostRepository;
use App\Service\Security\Token;

final class PostManager
{
    private PostRepository $postRepository;
    private Token $token;

    public function __construct(array $dataManager)
    {
        $this->postRepository = $dataManager['repository']['repoPage'];
        $this->token = $dataManager['token'];
    }

    public function showOne(int $dataId): ?Post
    {
        return $this->postRepository->findById($dataId);
    }

    public function getValidComment(int $postId): ?Comment
    {  
        return $this->postRepository->getComment($postId);
    }


    public function signalComment(array $datas)
    {
    
    }

    public function verifComment(int $id, string $user, array $data): ?array
    {
        if (isset($data['post']['submit'])) {
            
            $comment = htmlentities(strip_tags(trim($data['post']['comment'])));
            $idUser = $data['session']['idUser'];

            $errors = $data["errors"] ?? null;
            unset($data["errors"]);

            $success = $data["succes"] ?? null;
            unset($data["succes"]);

            if (empty($comment)) {
                $errors["errors"]['messageEmpty'] = "Veuillez mettre un commentaire";
            }

            if ($this->token->compareTokens($data) !== null) {
                $errors["errors"]['tokenEmpty'] = $this->token->compareTokens($data);
            }

            if (empty($errors)) {
                $this->postRepository->createComment($comment, $user, $idUser, $id);
                return $success["succes"]['send'] = 'Votre commentaire est en attente de validation';
            }
            return $errors;
        }
    }
}
