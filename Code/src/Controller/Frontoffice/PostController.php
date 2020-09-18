<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Controller\ErrorController;
use App\Model\Entity\Post;
use App\Model\Manager\PostManager;
use App\View\View;

final class PostController
{
    private ErrorController $error;
    private PostManager $postManager;
    private View $view;

    public function __construct(PostManager $postManager, View $view, ErrorController $error)
    {
        // Dépendances
        $this->postManager = $postManager;
        $this->view = $view;
        $this->error = $error;
    }
    
    public function PostAction(array $datas): void
    {
        $id = $datas['get']['id'] ?? null;
        $post = $this->postManager->showOne($id);
        $user = $this->postManager->showUser($id);
        if ($post instanceof Post) {
            $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user]);
        } else if ($post === null || empty($post)){
            $this->error->ErrorAction();
        }
    }
}
