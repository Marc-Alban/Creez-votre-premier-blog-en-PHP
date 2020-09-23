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

    public function __construct(UserManager $userManager, array $classController)
    {
        // Dépendances
        $this->userManager = $userManager;
        // $this->userManager = $manager;
        $this->view = $classController['view'];
        $this->error = $classController['error'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
    }

    public function UserAction(array $datas): void
    {
        
    }


}
