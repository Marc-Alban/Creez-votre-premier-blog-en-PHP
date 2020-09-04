<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\View\View;

class HomeController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function HomeAction()
    {
        $this->view->render('Frontoffice', 'home', []);
    }
}