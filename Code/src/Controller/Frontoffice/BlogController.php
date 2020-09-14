<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;
use App\Model\Manager\BlogManager;
use App\View\View;

class BlogController
{
    private View $view;
    private BlogManager $blogManager;

    public function __construct(BlogManager $blogManager, View $view)
    {
        $this->blogManager = $blogManager;
        $this->view = $view;
    }

    public function BlogAction(array $data): void
    {

        $lastPost = $this->blogManager->lastPost();

        if(isset($data['get']['pp']) && !empty($data['get']['pp'])){
            $paginationPost =  $this->blogManager->paginationPost($data);
        }else if(isset($data['get']['page']) && $data['get']['page'] === 'blog' && !isset($data['get']['pp']) && empty($data['get']['pp'])){
            header("Location: http://localhost:8000/index.php?page=blog&pp=1");
            exit();
        }


        $dataTable = [
            "paginationPost" => $paginationPost ?? null,
            'lastPost' => $lastPost,
        ];

        $this->view->render('Frontoffice', 'blog', $dataTable);
    }
}