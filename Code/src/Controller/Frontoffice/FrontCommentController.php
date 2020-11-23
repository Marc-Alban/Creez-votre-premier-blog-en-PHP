<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\CommentManager;
use App\Model\Manager\UserManager;
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
    public function sendAction(UserManager $userManager): void
    {
        // $idPost = (int) $this->request->getGet()->getName('id') ?? null;
        // $this->userSession = ($this->session->getSessionName('user'))? $this->session->getSessionName('user') : $this->session->getSessionName('admin');
        // $idUser = (int) $userManager->findUserByEmail($this->userSession)->getIdUser();
        // $this->session->setSession('token', $this->token->createSessionToken());
        // $this->commentManager->checkComment($idPost, $idUser, $this->request, $this->session, $this->token);
        // if (array_key_exists('success', $roleMessage)) {
        //     header("Refresh: 1;url=/?page=userManagement");
        //     if ($this->adminSession === $admin) {
        //         $accessControl->IsAdmin($this->session);
        //     }
        // }
    }
}
