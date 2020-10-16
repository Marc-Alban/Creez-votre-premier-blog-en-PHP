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
    
    public function errorServer(): void
    {
        $this->view->render('frontoffice', 'error', []);
    }

    public function notFound(): void
    {
        $this->view->render('frontoffice', '404', []);
    }
}
