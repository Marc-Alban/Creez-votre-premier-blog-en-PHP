<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;
use App\Service\Http\Session;

final class ErrorController
{
    private View $view;
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    
    public function ErrorAction(): void
    {
        $this->view->render('frontoffice','error', []);
    }
}
