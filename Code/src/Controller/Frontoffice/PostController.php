<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class PostController
{
    private PostManager $postManager;
    private View $view;
    private Token $token;
    private Session $session;
    private Request $request;
    public function __construct(PostManager $postManager, View $view, Request $request, Token $token, Session $session)
    {
        $this->postManager = $postManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
    }
    public function postAction(CommentManager $commentManager, UserManager $userManager): void
    {
        $id = (int) $this->request->getGet()->get('id') ?? null;
        $post = $this->postManager->findByIdPost($id);
        $comments = $commentManager->findByIdComment($post->getIdPost());
        $user = $userManager->findNameByIdUser($post->getUserId());
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, 'comment' => $comments]);
    }
    public function postsAction(): void
    {
        $perpage = (int) $this->request->getGet()->get('perpage') ?? null;
        $paginationPost =  $this->postManager->paginationPost($perpage) ?? null;
        if (empty($perpage) || $perpage === 0) {
            header('Location: /?page=posts&perpage=1');
            exit();
        } elseif (is_null($paginationPost['post'])) {
            header('Location: /?page=posts&perpage=1');
            exit();
        }
        $this->view->render('Frontoffice', 'posts', ['paginationPost' => $paginationPost]);
    }
    public function signalAction(CommentManager $commentManager): void
    {
        $id = (int) $this->request->getGet()->get('id') ?? null;
        $idComment = (int) $this->request->getGet()->get('idComment') ?? null;
        $commentManager->signalComment($idComment);
        header('Location: /?page=post&id='.$id);
        exit();
    }
    public function sendAction(CommentManager $commentManager): void
    {
        $id = (int) $this->request->getGet()->get('id') ?? null;
        $this->session->setSession('token', $this->token->createSessionToken());
        $commentManager->checkComment($id, $this->request, $this->session, $this->token);
    }
}
