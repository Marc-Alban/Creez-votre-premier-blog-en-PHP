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
    private ?string $userSession;
    private string $pseudo;
    private string $email;
    public function __construct(UserManager $userManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
        $this->userSession =  $this->session->getSessionName('user');
        $user = $this->userManager->findByUserEmail($this->userSession);
        if ($user !== null) {
            $this->pseudo = $user->getUserName();
            $this->email = $user->getEmail();
        }
    }
    /**
     * Display the dashboard page
     *
     * @return void
     */
    public function dashboardAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'dashboard', ['pseudo' => $this->pseudo,'email'=>$this->email]);
    }
    /**
     * method to modify a user
     *
     * @return void
     */
    public function updateUserAction(): void
    {
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $verifUser = $this->userManager->checkForm($this->session, $this->request, $this->token);
        $this->view->render('backoffice', 'dashboard', ['pseudo' => $this->pseudo,'email'=>$this->email, 'verif' => $verifUser]);
    }
    /**
     * Display the password page
     *
     * @return void
     */
    public function passwordAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'password', []);
    }
    /**
     * Methode to modify the password user
     *
     * @return void
     */
    public function updatePasswordAction(): void
    {
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $verifPassBdd = $this->userManager->checkPassword($this->session, $this->request, $this->token, $this->userSession);
        $this->view->render('backoffice', 'password', ['verif' => $verifPassBdd]);
    }
}
