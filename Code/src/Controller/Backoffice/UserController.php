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
    public function __construct(UserManager $userManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
    }
    public function dashboardAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        $userSession =  $this->session->getSessionName('user') ?? null;
        $user = $this->userManager->findByUserEmail($userSession);
        if (!isset($userSession) && $userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'dashboard', ['user' => $user]);
    }
    public function updateUserAction(): void
    {
        $userSession =  $this->session->getSessionName('user') ?? null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $verifUser = $this->userManager->checkForm($this->session, $this->request, $this->token);
        $user = $this->userManager->findByUserEmail($userSession);
        $verifUser = null;
        $this->view->render('backoffice', 'dashboard', ['user' => $user, 'verif' => $verifUser]);
    }
    public function passwordAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        $userSession =  $this->session->getSessionName('user') ?? null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'password', []);
    }
    public function updatePasswordAction(): void
    {
        $userSession =  $this->session->getSessionName('user') ?? null;
        $verifPassBdd = null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $verifPassBdd = $this->userManager->checkPassword($this->session, $this->request, $this->token, $userSession);
        $this->view->render('backoffice', 'password', ['verif' => $verifPassBdd]);
    }
    public function updatePostAction(): void
    {
        $userSession =  $this->session->getSessionName('user') ?? null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'modifPost', []);
    }
}
