<?php
declare(strict_types = 1);
namespace App\Controller\Frontoffice;

use App\Controller\ErrorController;
use App\Model\Manager\InscriptionManager;
use App\View\View;

class InscriptionController {

    private InscriptionManager $inscriptionManager;
    private View $view;
    private ErrorController $error;

    public function __construct(InscriptionManager $inscriptionManager, View $view, ErrorController $error)
    {
        $this->error = $error;
        $this->view = $view;
        $this->inscriptionManager = $inscriptionManager;
    }

    public function InscriptionAction(array $data): void
    {

        if(isset($data['session']['user']) && !empty($data['session']['user']))
        {
            header('Location: /?p=home');
            exit();
        }
        
        if(isset($data['get']['action']) && $data['get']['action'] === 'inscription')
        {
            $this->inscriptionManager->userSignIn($data);
        }else if(isset($data['get']['action']) && $data['get']['action'] !== 'inscription' && empty($data['get']['action'])){
            $this->error->ErrorAction();
        }
        $this->view->render('Frontoffice', 'inscription', []);
    }

}