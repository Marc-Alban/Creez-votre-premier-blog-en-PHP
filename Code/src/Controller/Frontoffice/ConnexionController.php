<?php
declare(strict_types = 1);
namespace App\Controller\Frontoffice;
use App\Model\Manager\ConnexionManager;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;
use App\Controller\ErrorController;

class ConnexionController {

    private View $view;
    private Token $token;
    private ErrorController $error;
    private Session $session;
    private ConnexionManager $connexionManager;

    public function __construct(array $classController)
    {
        $this->connexionManager = $classController['manager']['managerPage'];
        $this->view = $classController['view'];
        $this->error = $classController['error'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
    }

    public function ConnexionAction(array $data){
        $logIn = null;
        if (isset($data['get']['action']) && $data['get']['action'] === 'connexion') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $logIn = $this->connexionManager->verifUser($data);
        }else if(isset($data['get']['action']) && $data['get']['action'] !== 'connexion' && empty($data['get']['action'])){
            $this->error->ErrorAction();
        }
        return $this->view->render('Frontoffice', 'Connexion', ["logIn" => $logIn]);
    }

}