<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;
use App\Controller\ErrorController;
use App\Model\Manager\PasswordManager;

final class PasswordController
{
    private View $view;
    private Token $token;
    private ErrorController $error;
    private Session $session;
    private PasswordManager $passwordManager;

    public function __construct(array $classController)
    {
        // Dépendances
        $this->view = $classController['view']; 
        $this->error = $classController['error'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
        $this->passwordManager = $classController['manager']['managerPage'];
    }

    public function PasswordAction(array $data): void
    {
        $userSession = $data['session']['user'] ?? null;
        $verifPassBdd = null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        if (isset($data['get']['action']) && $data['get']['action'] === 'modifPass') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $verifPassBdd = $this->passwordManager->verifPass($data, $userSession);
        }else if(isset($data['get']['action']) && $data['get']['action'] !== 'modifPass' && empty($data['get']['action'])){
            $this->error->ErrorAction();
        }


        $this->view->render('backoffice', 'password', ['verif' => $verifPassBdd]);
    }

}