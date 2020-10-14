<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Entity\Post;
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
        $id = intval($this->request->getGet()->get('id')) ?? null;
        $action = $this->request->getGet()->get('action') ?? null;
        $post = $this->postManager->findByIdPost($id);
        $comments = $commentManager->findByIdComment($post->getIdPost());
        $user = null;
        $bugComment = null;
        if ($action === 'signal') {
            $idComment = intval($this->request->getGet()->get('idComment')) ?? null;
            $commentManager->signalComment($idComment);
            header('Location: /?page=post&id='.$id);
            exit();
        } elseif ($action === 'sendComment') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $bugComment = $commentManager->checkComment($id,$this->request, $this->session, $this->token);
        }
        if ($post instanceof Post) {
            $user = $userManager->findByIdUser($post->getUserId());
        } elseif ($post === null || empty($post)) {
            header('Location: /?page=post&id=1');
            exit();
        }
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, "bugComment" => $bugComment, 'comment' => $comments]);
    }
    public function postsAction(): void
    {
        $perpage = intval($this->request->getGet()->get('perpage')) ?? null;
        $page = $this->request->getGet()->get('page') ?? null;
        $paginationPost =  $this->postManager->paginationPost($perpage);
        if (isset($page) && $page === 'posts' && !isset($perpage)) {
            header('Location: /?page=posts&perpage=1');
            exit();
        } elseif (!isset($perpage) || empty($perpage) || $perpage === '0') {
            header('Location: /?page=posts&perpage=1');
            exit();
        } elseif ($paginationPost['post'] === null) {
            header('Location: /?page=posts&perpage=1');
            exit();
        }
        $this->view->render('Frontoffice', 'posts', ['paginationPost' => $paginationPost]);
    }
}
