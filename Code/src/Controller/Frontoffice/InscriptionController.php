<?php
declare(strict_types = 1);
namespace App\Controller\Frontoffice;

use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;
use App\Controller\ErrorController;
use App\Model\Manager\InscriptionManager;

class InscriptionController {

    private InscriptionManager $inscriptionManager;
    private View $view;
    private Token $token;
    private ErrorController $error;
    private Session $session;

    public function __construct(InscriptionManager $inscriptionManager, array $classController)
    {
        $this->view = $classController['view'];
        $this->error = $classController['error'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
        $this->inscriptionManager = $inscriptionManager;

    }

    public function InscriptionAction(array $data): void
    {
        $register = null;
        if(isset($data['session']['user']) && !empty($data['session']['user']))
        {
            header('Location: /?page=connexion');
            exit();
        }
        
        if (isset($data['get']['action']) && $data['get']['action'] === 'inscription') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $register = $this->inscriptionManager->userSignIn($data);
        }else if(isset($data['get']['action']) && $data['get']['action'] !== 'inscription' && empty($data['get']['action'])){
            $this->error->ErrorAction();
        }

        $this->view->render('Frontoffice', 'inscription', ["register" => $register]);
    }

}