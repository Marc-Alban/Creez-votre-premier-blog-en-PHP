<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Entity\Post;
use App\Model\Manager\PostManager;
use App\View\View;
use App\Controller\ErrorController;

final class PostController
{
    private PostManager $postManager;
    private View $view;
    private ErrorController $error;

    public function __construct(PostManager $postManager, View $view)
    {
        $this->postManager = $postManager;
        $this->view = $view;
        $this->error = new ErrorController;
    }
    
    public function displayOneAction(int $id): void
    {
        $data = $this->postManager->showOne($id);
        if ($data instanceof Post) {
            $this->view->render('frontoffice', 'post', ["post" => $data]);
        } elseif ($data === null) {
        $this->error->errorPost();
        }
    }
}
