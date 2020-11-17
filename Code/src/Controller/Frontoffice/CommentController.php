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
    private ?string $userSession;
    private ?string $adminSession;
    public function __construct(CommentManager $commentManager, Request $request, Token $token, Session $session)
    {
        $this->commentManager = $commentManager;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
        $this->userSession =  $this->session->getSessionName('user');
        $this->adminSession =  $this->session->getSessionName('admin');
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
        $userSession = ($this->session->getSessionName('user'))? $this->session->getSessionName('user') : $this->session->getSessionName('admin');
        $idUser = (int) $userManager->findUserByEmail($userSession)->getIdUser();
        $this->session->setSession('token', $this->token->createSessionToken());
        return $this->commentManager->checkComment($idPost, $idUser, $this->request, $this->session, $this->token);
    }
    /**
     * Return all comment in a page post with the idPost
     *
     */
    public function findAllPostCommentsAction(): void
    {
        // $idPost = (int) $this->request->getGet()->get('id');
        // $comments = $this->commentManager->findCommentByPostId($idPost);
        // foreach ($comments as $comment) {
        //     $user = $this->commentManager->findNameByUserId($comment->getUserId());
        //     $comment->setUser($user);
            // echo'<pre>';
            // var_dump(($comment->getUser())->getUserName());
            // echo'<pre>';
            // die();
        //}
    }
}
