<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Mail;
use App\Service\Security\Token;
use App\View\View;

final class UserController
{
    private UserManager $userManager;
    private View $view;
    private Session $session;
    private Request $request;
    private Token $token;
    public function __construct(UserManager $userManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
    }
    public function homeAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        $this->view->render('Frontoffice', 'home', []);
    }
    public function sendMailAction(Mail $mailClass): void
    {
        $mail = [];
        $mail = $mailClass->checkMail($this->session, $this->token, $this->request);
        if (array_key_exists("send", $mail)) {
            $mailClass->sendMail();
        }
        $this->view->render('Frontoffice', 'home', ['mail'=>$mail]);
    }
    public function logoutAction(): void
    {
        $this->session->sessionDestroy();
        header('Location: /?p=home');
        exit();
    }
    public function registerAction(): void
    {
        $user = $this->session->getSessionName('user') ?? null;
        $this->session->setSession('token', $this->token->createSessionToken());
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'register', []);
    }
    public function registrationAction(): void
    {
        $user = $this->session->getSessionName('user') ?? null;
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $checkRegister = null;
        $checkRegister = $this->userManager->userRegister($this->session, $this->token, $this->request);
        $this->view->render('Frontoffice', 'register', ["checkRegister" => $checkRegister]);
    }
    public function loginAction(): void
    {
        $user = $this->session->getSessionName('user') ?? null;
        $this->session->setSession('token', $this->token->createSessionToken());
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'login', []);
    }
    public function connectionAction(): void
    {
        $user = $this->session->getSessionName('user') ?? null;
        if (isset($user) && $user !== null) {
            header('Location: /?page=home');
            exit();
        }
        $logIn = null;
        $logIn = $this->userManager->userLogIn($this->session, $this->token, $this->request);
        $this->view->render('Frontoffice', 'login', ["logIn" => $logIn]);
    }
}
