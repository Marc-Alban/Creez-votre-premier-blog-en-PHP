<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

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
        $this->homeManager = $classController['manager'];
        $this->view = $classController['view'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
    }

    public function HomeAction(array $data)
    {
        $action = $data['get']['action'] ?? null;

        if(isset($action) && $action === 'sendMessage'){
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $this->mail = $this->homeManager->verifMail($data);
        }else if (isset($action) && $action === "logout") {
            $this->session->sessionDestroy();
            header('Location: ?p=home');
            exit();
        }


        $this->view->render('Frontoffice', 'home', ['mail' => $this->mail]);
    }
}