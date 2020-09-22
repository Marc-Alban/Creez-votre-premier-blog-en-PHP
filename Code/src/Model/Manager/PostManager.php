<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Entity\Post;
use App\Model\Entity\User;
use App\Model\Repository\PostRepository;

final class PostManager
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function showOne(?string $dataId): ?Post
    {
        return $this->postRepository->findById((int) $dataId);
    }

    public function showUser(?string $id ): ?User 
    {
        $idPost = intval($id);
        return $this->postRepository->joinUserPost($idPost);
    }

    public function signalComment(array $datas)
    {
    
    }

    public function verifComment(array $data)
    {
        if (isset($data['post']['submit'])) {
            $idUser = htmlentities(strip_tags(trim($data['session']['idUser'])));
            $comment = htmlentities(strip_tags(trim($data['post']['comment'])));
            $idPost = $data['get']['id'] ?? null;

            $errors = $data["session"]["error"] ?? null;
            unset($data["session"]["error"]);

            $succes = $data["session"]["succes"] ?? null;
            unset($data["session"]["succes"]);

            if (empty($comment)) {
                $errors['error']['messageEmpty'] = "Veuillez mettre un commentaire";
            }

            if ($this->token->compareTokens($data) !== null) {
                $errors['error']['tokenEmpty'] = $this->token->compareTokens($data);
            }

            if ($errors['error']['token'] === null) {
                unset($errors['error']['token']);
            }

            if (empty($errors)) {
                $succes['succes']['send'] = 'Votre commentaire est en attente de validation';
                $this->postRepository->createComment($idUser, $comment, $idPost);
            }
        }
    }
}
