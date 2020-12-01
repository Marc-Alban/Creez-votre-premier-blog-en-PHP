<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\Comment;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\UserRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class CommentManager
{
    private CommentRepository $commentRepository;
    private UserRepository $userRepository;
    private $errors = null;
    private $success = null;
    public function __construct(CommentRepository $commentRepository, UserRepository $userRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }
    /**
     * Comment validation method
     *
     * @param integer $idComment
     * @return array|null
     */
    public function valideComment(int $idComment): ?array
    {
        $valComment = $this->commentRepository->valide($idComment);
        if ($valComment === true) {
            $this->success['sendValide'] = "Commentaire validé";
            return $this->success;
        }
        return null;
    }
    /**
     * Comment delete method
     *
     * @param integer $idComment
     * @return array|null
     */
    public function deleteComment(int $idComment): ?array
    {
        $delComment = $this->commentRepository->delete($idComment);
        if ($delComment === true) {
            $this->errors['sendDelete'] = "Commentaire supprimé";
            return $this->errors;
        }
        return null;
    }
    /**
     * Pagination of the comment management page where all the comment are located
     *
     * @param integer $perpage
     * @return array
     */
    public function paginationComments(int $perpage = 1): array
    {
        $minComment = 10;
        $total = $this->commentRepository->total();
        $nbPage = (int) ceil($total/$minComment);
        if (ctype_digit($perpage) === true || $perpage <= 0) {
            $perpage = 1;
        } elseif ($perpage > $nbPage) {
            $perpage = $nbPage;
        }
        $page =  ($perpage-1) * $minComment;
        $comment = $this->commentRepository->findAll($page, $minComment);
        return [
            'current' => $perpage,
            'nbPage' => $nbPage,
            'comment' => $comment
        ];
    }
    /**
     * Returns the number of comments as a number not online
     *
     * @return integer|null
     */
    public function countAllCommentsDisabled(): ?int
    {
        $commentsDisabled = $this->commentRepository->count(1);
        if ($commentsDisabled !== null) {
            return $commentsDisabled;
        }
        return null;
    }
    /**
     * Returns the number of comments as a number online
     *
     * @return integer|null
     */
    public function countAllComments(): ?int
    {
        $comments = $this->commentRepository->count(0);
        if ($comments !== null) {
            return $comments;
        }
        return null;
    }
    /**
     * Get comment with the post id
     *
     * @param integer $postId
     * @return array|null
     */
    public function findCommentByPostId(int $postId): ?array
    {
        $comments = $this->commentRepository->findByPostId($postId);
        return $comments;
    }
    /**
     * Checking the comment form and returning an error or not if the form is bad
     *
     * @param integer $idComment
     * @param string $userSession
     * @param Request $request
     * @param Session $session
     * @param Token $token
     * @return void
     */
    public function checkComment(int $idComment, string $userSession, Request $request, Session $session, Token $token): void
    {
        $post = $request->getPost();
        $comment = $post->getName('comment');
        $user = $this->userRepository->findByEmail($userSession);
        if (empty($comment)) {
            $session->setSession('error', "Veuillez mettre un commentaire") ;
        }
        if ($token->compareTokens($session->getSessionName('token'), $post->getName('token')) !== false) {
            $session->setSession('error', "Formulaire incorrect") ;
        }
        if (empty($session->getSessionName('error'))) {
            $this->commentRepository->create($comment, $user->getIdUser(), $idComment);
            $session->setSession('success', "Votre commentaire est en attente de validation");
        }
    }
}
