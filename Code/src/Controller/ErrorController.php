<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;

final class ErrorController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View;
    }
    
    public function errorPost(): void
    {
        $this->view->render('frontoffice','error', []);
    }
}
