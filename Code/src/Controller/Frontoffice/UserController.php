<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

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
    private $action;
    public function __construct(UserManager $userManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
        $this->action = $request->getGet()->get('action') ?? null;
    }
    /**
     * Return page home
     *
     * @return void
     */
    public function homeAction(): void
    {
        $mail = null;
        if (isset($this->action) && $this->action === 'sendMessage') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $mail = $this->userManager->verifMail($this->session, $this->token, $this->request, $this->action);
        } elseif (isset($this->action) && $this->action === "logout") {
            $this->session->sessionDestroy();
            header('Location: /?p=home');
            exit();
        }
        $this->view->render('Frontoffice', 'home', ['mail' => $mail]);
    }
    public function inscriptionAction(): void
    {
        $user = $this->session->getSession('user') ?? null;
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $register = null;
        if (isset($this->action) && $this->action === 'inscription') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $register = $this->userManager->userSignIn($this->session, $this->token, $this->request, $this->action);
        } elseif (isset($this->action) && $this->action !== 'inscription' && empty($this->action)) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'inscription', ["register" => $register]);
    }
    public function connexionAction(): void
    {
        $user = $this->session->getSession('user') ?? null;
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $logIn = null;
        if (isset($this->action) && $this->action === 'connexion') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $logIn = $this->userManager->verifUser($this->session, $this->token, $this->request, $this->action);
        } elseif (isset($this->action) && $this->action !== 'connexion' && empty($this->action)) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'Connexion', ["logIn" => $logIn]);
    }
}
