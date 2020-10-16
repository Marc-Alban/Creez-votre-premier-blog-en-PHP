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
    public function __construct(UserManager $userManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
        $this->action = $request->getGet()->get('action') ?? null;
    }
    public function homeAction(MailManager $mailManager = null): void
    {
        $mail = null;
        if ($this->action === 'sendMessage') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $mail = $mailManager->checkMail($this->session, $this->token, $this->request, $this->action);
            if ($mail === ['succes']) {
                $mailManager->sendMail($this->request);
            }
        } elseif ($this->action === "logout") {
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
        if ($this->action === 'inscription') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $register = $this->userManager->userSignIn($this->session, $this->token, $this->request, $this->action);
        } elseif ($this->action !== 'inscription' && empty($this->action)) {
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
        if ($this->action === 'connexion') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $logIn = $this->userManager->checkUser($this->session, $this->token, $this->request, $this->action);
        } elseif ($this->action !== 'connexion' && empty($this->action)) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'Connexion', ["logIn" => $logIn]);
    }
}
