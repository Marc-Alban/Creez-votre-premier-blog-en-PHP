<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;
use App\Controller\ErrorController;

final class AddPostController
{
    private View $view;
    private Token $token;
    private ErrorController $error;
    private Session $session;

    public function __construct(array $classController)
    {
        // Dépendances
        $this->view = $classController['view'] ?? null; 
        $this->error = $classController['error'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
    }

    public function AddPostAction(array $datas): void
    {
        $userSession = $datas['session']['user'] ?? null;
        $user = null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        $this->view->render('backoffice', 'addPost', []);
    }

}