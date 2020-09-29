<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\DashboardManager;
use App\Service\Http\Session;

final class DashboardController
{
    private DashboardManager $dashboardManager;
    private Session $session;

    public function __construct(array $classController)
    {
        // Dépendances
        $this->dashboardManager = $classController['manager']['managerPage'] ?? null;
        $this->session = $classController['session'] ?? null;
    }

    public function DashboardAction(array $datas): void
    {
        echo 'Bienvenu sur la page dahsboard';
    }

}