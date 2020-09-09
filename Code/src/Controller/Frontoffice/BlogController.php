<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\View\View;

class BlogController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function BlogAction()
    {
        $this->view->render('Frontoffice', 'blog', []);
    }
}