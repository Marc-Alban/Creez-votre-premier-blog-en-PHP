<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\Model\Manager\UserManager;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;

final class CommentController
{
    private CommentManager $commentManager;
    private Token $token;
    private Session $session;
    private Request $request;
    
    public function __construct(CommentManager $commentManager,  Request $request, Token $token, Session $session)
    {
        $this->commentManager = $commentManager;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
    }
    public function sendAction(UserManager $userManager): array
    {
        $idPost = (int) $this->request->getGet()->get('id') ?? null;
        $idUser = (int) $userManager->findByUserEmail($this->session->getSessionName('user'))->getIdUser();
        $this->session->setSession('token', $this->token->createSessionToken());
        return $this->commentManager->checkComment($idPost, $idUser, $this->request, $this->session, $this->token);
    }
    public function findAllPostCommentsAction(PostManager $postManager): ?array
    {
        return $this->commentManager->findByIdPost($postManager->findIdPost($this->request));
    }
    public function findUserNameByIdCommentAction(): string 
    {
        $idPost = (int) $this->request->getGet()->get('id');
        return $userName = $this->commentManager->findUserNameCommentByIdPost($idPost);
    }
}