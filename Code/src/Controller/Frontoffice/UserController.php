<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Model\Manager\MailManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class UserController
{
    private UserManager $userManager;
    private View $view;
    private Session $session;
    private Request $request;
    private Token $token;
    private $action;
    private $sessionToken;
    public function __construct(UserManager $userManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
        $this->action = $request->getGet()->get('action') ?? null;
        $this->sessionToken =  $this->session->setSession('token', $this->token->createSessionToken());
    }
    public function homeAction(MailManager $mailManager = null): void
    {
        $mail = null;
        if (isset($this->action) && $this->action === 'sendMessage') {
            $this->sessionToken;
            $mail = $mailManager->checkMail($this->session, $this->token, $this->request, $this->action);
            var_dump($mail);
            die();
            // if($mail === )
        } elseif (isset($this->action) && $this->action === "logout") {
            $this->session->sessionDestroy();
            header('Location: /?p=home');
            exit();
        }
        $this->view->render('Frontoffice', 'home', ['mail' => $mail]);
    }
    public function inscriptionAction(): void
    {
        $user = $this->session->getSessionName('user') ?? null;
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $register = null;
        if (isset($this->action) && $this->action === 'inscription') {
            $this->sessionToken;
            $register = $this->userManager->userSignIn($this->session, $this->token, $this->request, $this->action);
        } elseif (isset($this->action) && $this->action !== 'inscription' && empty($this->action)) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'inscription', ["register" => $register]);
    }
    public function connexionAction(): void
    {
        $user = $this->session->getSessionName('user') ?? null;
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $logIn = null;
        if (isset($this->action) && $this->action === 'connexion') {
            $this->sessionToken;
            $logIn = $this->userManager->checkUser($this->session, $this->token, $this->request, $this->action);
        } elseif (isset($this->action) && $this->action !== 'connexion' && empty($this->action)) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'Connexion', ["logIn" => $logIn]);
    }
}
