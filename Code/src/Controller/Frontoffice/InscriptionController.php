<?php
declare(strict_types = 1);
namespace App\Controller\Frontoffice;

use App\Service\Http\Session;
use App\Service\Security\Token;
use App\Controller\ErrorController;
use App\Model\Manager\InscriptionManager;
use App\View\View;

class InscriptionController {

    private InscriptionManager $inscriptionManager;
    private View $view;
    private Token $token;
    private ErrorController $error;
    private Session $session;

    public function __construct(InscriptionManager $inscriptionManager, View $view, ErrorController $error, Token $token,Session $session)
    {
        $this->error = $error;
        $this->view = $view;
        $this->token = $token;
        $this->inscriptionManager = $inscriptionManager;
        $this->session = $session;
    }

    public function InscriptionAction(array $data): void
    {
        $register = null;
        if(isset($data['session']['user']) && !empty($data['session']['user']))
        {
            header('Location: /?p=home');
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