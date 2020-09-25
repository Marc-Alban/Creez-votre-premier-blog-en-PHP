<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Controller\ErrorController;
use App\Model\Manager\HomeManager;
use App\View\View;
use App\Service\Security\Token;
use App\Service\Http\Session;

class HomeController
{
    private View $view;
    private HomeManager $homeManager;
    private Session $session;
    private Token $token;
    private array $mail = [];

    
    public function __construct(array $classController)
    {
        $this->homeManager = $classController['manager']['managerPage'];
        $this->view = $classController['view'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
    }

    public function HomeAction(array $data)
    {
        if(isset($data['get']['action']) && $data['get']['action'] === 'sendMessage'){
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $this->mail = $this->homeManager->verifMail($data);
        }
        $this->view->render('Frontoffice', 'home', ['mail' => $this->mail]);
    }
}