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
        header("HTTP/1.0 500 Internal Server Error");
        $this->view->render('execption', 'error500', []);
    }
    /**
     * Error, if the page is not found
     *
     * @return void
     */
    public function notFound(): void
    {
        header("HTTP/1.0 404 Not Found");
        $this->view->render('execption', 'error404', []);
    }
}
