<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\UserManager;
use App\View\View;
use App\Service\Security\Token;
use App\Controller\ErrorController;
use App\Service\Http\Session;
use App\Service\Http\Request;

final class UserController
{
    private UserManager $userManager;
    private View $view;
    private Token $token;
    private ErrorController $error;
    private Session $session;
    private Request $request;

    public function __construct(UserManager $userManager, View $view, ErrorController $error, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->error = $error;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
        $this->post = $this->request->getPost()->get('post') ?? null;
        $this->action = $this->request->getGet()->get('action') ?? null;
    }

    public function dashboardAction(): void
    {
        $userSession = $data['session']['user'] ?? null;
        $user = $this->userManager->getDataUser();
        $verifUser = null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        if (isset($data['get']['action']) && $data['get']['action'] === 'sendDatasUser') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $verifUser = $this->userManager->verifForm($data);
        }else if(isset($data['get']['action']) && $data['get']['action'] !== 'sendDatasUser' && empty($data['get']['action'])){
            $this->error->notFound();
        }

        $this->view->render('backoffice', 'dashboard', ['user' => $user, 'verif' => $verifUser]);
    }

    public function passwordAction(): void
    {
        $userSession = $data['session']['user'] ?? null;
        $verifPassBdd = null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        if (isset($data['get']['action']) && $data['get']['action'] === 'modifPass') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $verifPassBdd = $this->userManager->verifPass($data, $userSession);
        }else if(isset($data['get']['action']) && $data['get']['action'] !== 'modifPass' && empty($data['get']['action'])){
            $this->error->notFound();
        }

        $this->view->render('backoffice', 'password', ['verif' => $verifPassBdd]);
    }

    public function updatePostAction(): void
    {
        $userSession = $datas['session']['user'] ?? null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        $this->view->render('backoffice', 'modifPost', []);
    }

}