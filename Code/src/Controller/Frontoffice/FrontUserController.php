<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Mail;
use App\Service\Security\Token;
use App\View\View;

final class FrontUserController
{
    private UserManager $userManager;
    private View $view;
    private Session $session;
    private Request $request;
    private Token $token;
    private ?string $userSession;
    private ?string $adminSession;
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
        $this->adminSession =  $this->session->getSessionName('admin');
    }
    /**
     * display the home page
     *
     * @return void
     */
    public function homeAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        $this->view->render('Frontoffice', 'home', []);
    }
    /**
     * method to send an email and display the home page
     *
     * @param Mail $mailClass
     * @return void
     */
    public function sendMailAction(Mail $mailClass): void
    {
        $mail = [];
        $mail = $mailClass->checkMail($this->session, $this->token, $this->request);
        if (array_key_exists("send", $mail)) {
            $mailClass->sendMail();
        }
        $this->view->render('Frontoffice', 'home', ['mail'=>$mail]);
    }
    /**
     * method to disconnect the user from his account
     *
     * @return void
     */
    public function logoutAction(): void
    {
        $this->session->sessionDestroy();
        header('Location: /?p=home');
        exit();
    }
    /**
     * display the register page
     *
     * @return void
     */
    public function registerAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if ($this->userSession !== null || $this->adminSession !== null) {
            header('Location: /?page=home');
            exit();
        }
        
        $this->view->render('Frontoffice', 'register', []);
    }
    /**
     * method to save the user for his account
     *
     * @return void
     */
    public function registrationAction(): void
    {
        if ($this->userSession !== null || $this->adminSession !== null) {
            header('Location: /?page=home');
            exit();
        }
        $checkRegister = $this->userManager->userRegister($this->session, $this->token, $this->request);
        if (array_key_exists('success', $checkRegister) === true) {
            header('Location: /?page=accountManagement');
            exit();
        }
        $this->pseudo = $this->request->getPost()->getName('userName');
        $this->email = $this->request->getPost()->getName('email');
        $this->view->render('Frontoffice', 'register', ["checkRegister" => $checkRegister,'email' => $this->email,'pseudo'=>$this->pseudo]);
    }
    /**
     * display the login page
     *
     * @return void
     */
    public function loginAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if ($this->userSession !== null || $this->adminSession !== null) {
            header('Location: /?page=home');
            exit();
        }
        $this->view->render('Frontoffice', 'login', []);
    }
    /**
     * method to connect a user
     *
     * @return void
     */
    public function connectionAction(): void
    {
        if ($this->userSession !== null || $this->adminSession !== null) {
            header('Location: /?page=home');
            exit();
        }
        $checkConnection = $this->userManager->userLogIn($this->session, $this->token, $this->request);
        if (array_key_exists('success', $checkConnection) === true) {
            if ($this->session->getSessionName('user') && !$this->session->getSessionName('admin')) {
                header('Location: /?page=accountManagement');
                exit();
            } elseif (!$this->session->getSessionName('user') && $this->session->getSessionName('admin')) {
                header('Location: /?page=dashboard');
                exit();
            }
        }
        $this->email = $this->request->getPost()->getName('email');
        $this->view->render('Frontoffice', 'login', ["checkConnection" => $checkConnection,'email' => $this->email]);
    }
}
