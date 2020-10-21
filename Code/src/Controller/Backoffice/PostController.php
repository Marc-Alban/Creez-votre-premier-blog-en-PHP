<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\PostManager;
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
    public function __construct(PostManager $postManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->postManager = $postManager;
        $this->request = $request;
    }
    public function addPostAction(): void
    {
        $userSession = $this->session->getSessionName('user') ?? null;
        $action = $this->request->getGet()->get('action') ?? null;
        $valdel = null;
        if ($userSession === null) {
            header('Location: /connexion');
            exit();
        }
        if ($action === 'addPost') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $valdel = $this->postManager->checkFormAddPost($this->session, $this->token, $this->request);
        }
        $this->view->render('backoffice', 'addPost', ["valdel" => $valdel]);
    }
    public function allPostAction(): void
    {
        $userSession = $this->session->getSessionName('user') ?? null;
        $perpage = intval($this->request->getGet()->get('perpage')) ?? null;
        if ($userSession === null) {
            header('Location: /connexion');
            exit();
        } elseif (!is_int($perpage) || empty($perpage)) {
            header('Location: /allPost&perpage=1');
            exit();
        }
        $post = $this->postManager->paginationPost($perpage);
        $this->view->render('backoffice', 'allPost', ['allPosts' => $post]);
    }
}
