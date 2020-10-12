<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class CommentManager
{
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }
    public function getAllComments(): ?array
    {
        return $this->commentRepository->getAllCommentBdd();
    }
    public function validedComment(int $idComment, int $signal = null, Session $session): ?array
    {
        $validation =  $session['valide'] ?? null;
        unset($validation);
        $validComment = $this->commentRepository->validedCommentBdd($idComment, $signal);
        if ($validComment === true) {
            $validation['sendValide'] = "Commentaire validé";
            return $validation;
        }
        return null;
    }
    public function deletedComment(int $idComment, Session $session): ?array
    {
        $suppression =  $session['deleted'] ?? null;
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
    public function verifComment(int $id, string $user, Request $request, Session $session, Token $token): ?array
    {
        $post = $request->getPost() ?? null;
        $submit = $post->get('submit') ?? null;
        $get = $request->getGet() ?? null;
        if (isset($submit) && $get->get('action') === 'sendComment') {
            $comment = $post->get('comment');
            $idUser = $session['idUser'];
            $errors =  $session["errors"] ?? null;
            unset($session["errors"]);
            $success =  $session["succes"] ?? null;
            unset($data["succes"]);
            if (empty($comment)) {
                $errors["errors"]['messageEmpty'] = "Veuillez mettre un commentaire";
            }
            if ($token->compareTokens($session, $post->get('token')) !== null) {
                $errors["errors"]['tokenEmpty'] = $this->token->compareTokens($session, $post->get('token'));
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
