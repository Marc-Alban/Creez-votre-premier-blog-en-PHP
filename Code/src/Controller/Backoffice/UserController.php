<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class UserController
{
    private UserManager $userManager;
    private View $view;
    private Token $token;
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
        $this->action = $this->request->getGet()->get('action') ?? null;
    }
    public function dashboardAction(): void
    {
        $userSession =  $this->session->getSessionName('user') ?? null;
        $user = $this->userManager->findAllUser();
        $verifUser = null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /connexion');
            exit();
        }
        if (isset($this->action) && $this->action === 'sendDatasUser') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $verifUser = $this->userManager->checkForm($this->session, $this->request, $this->token);
        } elseif (isset($this->action) && $this->action !== 'sendDatasUser' && empty($this->action)) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('backoffice', 'dashboard', ['user' => $user, 'verif' => $verifUser]);
    }
    public function passwordAction(): void
    {
        $userSession =  $this->session->getSessionName('user') ?? null;
        $verifPassBdd = null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /connexion');
            exit();
        }
        if (isset($this->action) && $this->action === 'modifPass') {
            $this->session->setSession('token', $this->token->createSessionToken());
            $verifPassBdd = $this->userManager->checkPassword($this->session, $this->request, $this->token, $userSession);
        } elseif (isset($this->action) && $this->action !== 'modifPass' && empty($this->action)) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('backoffice', 'password', ['verif' => $verifPassBdd]);
    }
    public function updatePostAction(): void
    {
        $userSession =  $this->session->getSessionName('user') ?? null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /connexion');
            exit();
        }
        $this->view->render('backoffice', 'modifPost', []);
    }
}
