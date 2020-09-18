<?php
declare(strict_types = 1);
namespace App\Controller\Frontoffice;
use App\Model\Manager\InscriptionManager;
use App\View\View;
use App\Controller\ErrorController;

class InscriptionController {

    private View $view;
    private InscriptionManager $inscriptionManager;
    private ErrorController $error;

    public function __construct(InscriptionManager $nscriptionManager, View $view, ErrorController $error)
    {
        $this->view = $view;
        $this->inscriptionManager = $inscriptionManager;
        $this->error = $error;
    }

    public function InscriptionAction(array $data): void
    {
        if(isset($data['session']['user']) && !empty($data['session']['user']))
        {
            header('Location: /?p=home');
            exit();
        }
        else if(!isset($data['session']['user']) && empty($data['session']['user']))
        {
            $this->inscriptionManager->userSignIn($data);
        }
        $this->view->render('Frontoffice', 'Inscription', []);
    }

}