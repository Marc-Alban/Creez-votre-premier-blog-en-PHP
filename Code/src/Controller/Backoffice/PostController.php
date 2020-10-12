<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\PostManager;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class PostController
{
    private PostManager $postManager;
    private View $view;
    private Token $token;
    private Session $session;

    public function __construct(PostManager $postManager, View $view, Token $token, Session $session)
    {
        $this->view = $view; 
        $this->token = $token;
        $this->session = $session;
        $this->postManager = $postManager;
    }

    public function addPostAction(): void
    {
        $userSession = $datas['session']['user'] ?? null;
        $action = $datas['get']['action'] ?? null;
        $valdel = null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        if(isset($action) && $action === 'addPost'){
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $valdel = $this->postManager->verifFormAddPost($datas);
        }

        $this->view->render('backoffice', 'addPost', ["valdel" => $valdel]);
    }

    public function allPostAction(): void
    {
        $userSession = $datas['session']['user'] ?? null;
        $pp = intval($datas['get']['pp']) ?? null;

        if(!isset($userSession) || $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }else if(!isset($pp) || $pp === null || empty($pp) || is_string($pp)){
            header('Location: /?page=allPost&pp=1');
            exit(); 
        }

        $post = $this->BlogManager->paginationPost($datas);

        $this->view->render('backoffice', 'allPost', ['allPosts' => $post]);
    }

}