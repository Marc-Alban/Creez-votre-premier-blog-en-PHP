<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\CommentManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class CommentController
{
    private CommentManager $commentManager;
    private Token $token;
    private Session $session;
    private Request $request;
    public function __construct(CommentManager $commentManager, Request $request, Token $token, Session $session)
    {
        $this->commentManager = $commentManager;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
    }
    /**
     * Send a comment in the page post
     *
     * @param UserManager $userManager
     * @return array
     */
    public function sendAction(UserManager $userManager): array
    {
        $idPost = (int) $this->request->getGet()->get('id') ?? null;
        $idUser = (int) $userManager->findByUserEmail($this->session->getSessionName('user'))->getIdUser();
        $this->session->setSession('token', $this->token->createSessionToken());
        return $this->commentManager->checkComment($idPost, $idUser, $this->request, $this->session, $this->token);
    }
    /**
     * Return all comment in a page post with the idPost
     *
     * @return array|null
     */
    public function findAllPostCommentsAction(): ?array
    {
        $idPost = (int) $this->request->getGet()->get('id');
        return $this->commentManager->findCommentByPostId($idPost);
    }
}
