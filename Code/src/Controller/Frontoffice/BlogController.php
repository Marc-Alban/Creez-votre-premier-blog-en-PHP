<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Controller\ErrorController;
use App\Model\Manager\BlogManager;
use App\View\View;

class BlogController
{
    private View $view;
    private BlogManager $blogManager;
    private ErrorController $errorController;

    public function __construct(array $classController)
    {

        $this->blogManager = $classController['manager']['managerPage'];
        $this->view = $classController['view'];
        $this->error = $classController['error'];
    }

    public function BlogAction(array $data): void
    {
        if (isset($data['get']['pp']) && !empty($data['get']['pp'])) {
            $paginationPost =  $this->blogManager->paginationPost($data);
        }else if(isset($data['get']['page']) && $data['get']['page'] === 'blog' && !isset($data['get']['pp'])){
            header('Location: /?page=blog&pp=1');
            exit();
        }else if(isset($data['get']['page']) || !isset($data['get']['pp']) || empty($data['get']['pp']) || $data['get']['pp'] !== '0'){
            header('Location: /?page=blog&pp=1');
            exit();
        }
        

        $dataTable = [
            "paginationPost" => $paginationPost ?? null,
        ];

        $this->view->render('Frontoffice', 'blog', $dataTable);
    }
}