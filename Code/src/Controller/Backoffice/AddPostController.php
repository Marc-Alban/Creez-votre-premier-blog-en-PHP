<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;
use App\Model\Manager\AddPostManager;

final class AddPostController
{
    private View $view;
    private Token $token;
    private Session $session;
    private AddPostManager $AddPostManager;

    public function __construct(array $classController)
    {
        // Dépendances
        $this->view = $classController['view'] ?? null; 
        $this->token = $classController['token'];
        $this->session = $classController['session'];
        $this->AddPostManager = $classController['manager']['managerPage'];
    }

    public function AddPostAction(array $datas): void
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
            $valdel = $this->AddPostManager->verifFormAddPost($datas);
        }

        $this->view->render('backoffice', 'addPost', ["valdel" => $valdel]);
    }

}