<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;


final class ErrorController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }
    
    public function ErrorAction(): void
    {
        $this->view->render('frontoffice','error', []);
    }

    public function ErrorBdd(): void
    {
        $bdd = 'Problème avec la connexion de la base de donnée';
        $this->view->render('frontoffice','error', ['bdd' => $bdd]);
    }
}
