<?php
declare(strict_types = 1);
namespace App\Controller\Frontoffice;
use App\Model\Manager\ConnexionManager;
use App\View\View;

class ConnexionController {

    private View $view;

    public function __construct(ConnexionManager $connexionManager, View $view)
    {
        $this->view = $view;
    }

    public function ConnexionAction(){
        return $this->view->render('Frontoffice', 'Connexion', []);
    }

}