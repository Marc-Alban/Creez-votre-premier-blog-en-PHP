<?php

declare(strict_types=1);

namespace App\Controller\Frontoffice;

use App\Model\Manager\UserManager;
use App\View\View;
use App\Controller\ErrorController;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class UserController
{
    private UserManager $userManager;
    private View $view;
    private ErrorController $error;
    private Session $session;

    public function __construct(array $classController)
    {
        // Dépendances
        $this->userManager = $classController['manager'] ?? null;
        $this->view = $classController['view'] ?? null;
        $this->error = $classController['error'] ?? null;
        $this->token = $classController['token'] ?? null;
        $this->session = $classController['session'] ?? null;
    }

    public function UserAction(array $datas): void
    {

    }

}
