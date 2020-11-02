<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Repository\CommentRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class CommentManager
{
    private CommentRepository $commentRepository;
    private $errors = null;
    private $succes = null;
    
    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function validComment(int $idComment, int $signal = null): ?array
    {
        $validComment = $this->commentRepository->valid($idComment, $signal);
        if ($validComment === true) {
            $this->succes['sendValide'] = "Commentaire validé";
            return $this->succes;
        }
        return null;
    }
    public function deleteComment(int $idComment): ?array
    {
        $delComment = $this->commentRepository->delete($idComment);
        if ($delComment === true) {
            $this->errors['sendDelete'] = "Commentaire supprimé";
            return $this->errors;
        }
        return null;
    }
    public function findAllComments(): ?array
    {
        return $this->commentRepository->findAll();
    }
    public function findByIdComment(int $postId): ?array
    {
        return  $this->commentRepository->findById($postId);
    }
    public function signalComment(int $idComment): void
    {
        $this->commentRepository->signal($idComment);
    }
    public function checkComment(int $idComment, Request $request, Session $session, Token $token): ?array
    {
        $post = $request->getPost() ?? null;
        $get = $request->getGet() ?? null;
        if ($get->get('action') === 'sendComment') {
            $comment = $post->get('comment');
            if (empty($comment)) {
                $this->errors["error"]['messageEmpty'] = "Veuillez mettre un commentaire";
            }
            if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
                $this->errors['error']['formRgister'] = "Formulaire incorrect";
            }
            if (empty($this->errors)) {
                $this->succes["succes"]['send'] = 'Votre commentaire est en attente de validation';
                $this->commentRepository->create($comment, $session->getSessionName('user'), $session->getSession()['idUser'], $idComment);
                return $this->succes;
            }
            return $this->errors;
        }
        return null;
    }
}
