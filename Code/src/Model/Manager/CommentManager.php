<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Repository\CommentRepository;
use App\Service\Http\Request;

final class CommentManager
{
    private CommentRepository $commentRepository;
    private Request $request;

    public function __construct(CommentRepository $commentRepository, Request $request)
    {
        $this->commentRepository = $commentRepository;
        $this->request = $request;
    }

    public function getAllComments(): ?array
    {
        return $this->commentRepository->getAllCommentBdd();
    }

    public function validedComment(int $idComment, int $signal = null, array $datas): ?array
    {
        $validation =  $datas['session']['valide'] ?? null;
        unset($validation);
        $validComment = $this->commentRepository->validedCommentBdd($idComment, $signal);
        if($validComment === true){
            $validation['sendValide'] = "Commentaire validé";
            return $validation;
        }
        return null;
    }

    public function deletedComment(int $idComment, array $datas): ?array
    {
        $suppression =  $datas['session']['deleted'] ?? null;
        unset($suppression);
        $delComment = $this->commentRepository->deletedCommentBdd($idComment);
        if ($delComment === true) {
            $suppression['sendDelete'] = "Commentaire supprimé";
            return $suppression;
        }
        return null;
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