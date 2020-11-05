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
    /**
     * Error, if the server is dead
     *
     * @return void
     */
    public function errorServer(): void
    {
        $this->view->render('frontoffice', 'error', []);
    }
    /**
     * Error, if the page is not found
     *
     * @return void
     */
    public function notFound(): void
    {
        $this->view->render('frontoffice', '404', []);
    }
}
