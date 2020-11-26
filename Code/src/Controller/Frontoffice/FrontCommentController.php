<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\CommentManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class FrontCommentController
{
    private CommentManager $commentManager;
    private Token $token;
    private Session $session;
    private Request $request;
    private ?string $userSession;
    public function __construct(CommentManager $commentManager, Request $request, Token $token, Session $session)
    {
        $this->commentManager = $commentManager;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
    }
    /**
     * Send a comment in the page post
     */
    public function sendCommentAction(int $idPost): void
    {
        $this->userSession = ($this->session->getSessionName('user'))? $this->session->getSessionName('user') : $this->session->getSessionName('admin');
        $validation = $this->commentManager->checkComment($idPost, $this->userSession, $this->request, $this->session, $this->token);
        $this->session->setSession('validation', $validation);
        header('Location: /?page=post&validation=valide&id='.$idPost);
        exit();
    }
}
