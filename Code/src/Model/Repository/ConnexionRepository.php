<?php
declare(strict_types=1);
namespace App\Repository;
use App\Model\Manager\ConnexionManager;
use App\View\View;
use App\Controller\ErrorController;

class ConnexionRepository
{
    private View $view;
    private ConnexionManager $ConnexionManager;
    private ErrorController $error;

    public function __construct(ConnexionManager $ConnexionManager, View $view, ErrorController $error)
    {
        $this->view = $view;
        $this->ConnexionManager = $ConnexionManager;
        $this->error = $error;
    }
}