<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\Post;
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

    public function getAllComment(int $postId): ?array
    {  
        return  $this->postRepository->getComment($postId);
    }

    public function signalComment(int $idComment): void
    {
        $this->postRepository->signalCommentBdd($idComment);
    }

    public function verifComment(int $id, string $user, array $data): ?array
    {
        if (isset($data['post']['submit']) && $data['get']['action'] === 'sendComment') {
            
            $comment = htmlentities(trim($data['post']['comment']));
            $idUser = $data['session']['idUser'];

            $errors =  $data['session']["errors"] ?? null;
            unset( $data['session']["errors"]);

            $success =  $data['session']["succes"] ?? null;
            unset($data["succes"]);

            if (empty($comment)) {
                $errors["errors"]['messageEmpty'] = "Veuillez mettre un commentaire";
            }

            if ($this->token->compareTokens($data) !== null) {
                $errors["errors"]['tokenEmpty'] = $this->token->compareTokens($data);
            }

            if (empty($errors)) {
                $success["succes"]['send'] = 'Votre commentaire est en attente de validation';
                $this->postRepository->createComment($comment, $user, $idUser, $id);
                return $success;
            }
            return $errors;
        }
        return null;
    }
}
