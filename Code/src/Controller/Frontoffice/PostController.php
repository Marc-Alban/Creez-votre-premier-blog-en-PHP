<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Controller\ErrorController;
use App\Model\Entity\Post;
use App\Model\Repository\PostRepository;
use App\Model\Manager\PostManager;
use App\Service\Database;
use App\Service\ConfigProperties;
use App\View\View;

final class PostController
{
    private ErrorController $error;
    private ConfigProperties $configProperties;
    private Database $db;
    private PostManager $postManager;
    private PostRepository $postRepo;
    private View $view;

    public function __construct(PostManager $postManager, View $view, ConfigProperties $configProperties, Database $database)
    {
        $this->postManager = $postManager;
        $this->view = $view;
        $this->configProperties = $configProperties;
        // Dépendances
        $this->error = new ErrorController();
        // Injection des dépendances
        $database = $database($this->configProperties);
        $this->db = $database;
        $this->postRepo = new PostRepository($this->db);
        $postManager = $postManager($this->postRepo);
        $this->postManager = $postManager;
    }
    
    public function PostAction(array $datas): void
    {
        $id = $datas['get']['id'] ?? null;
        $data = $this->postManager->showOne($id);
        if ($data instanceof Post) {
            $this->view->render('frontoffice', 'post', ["post" => $data]);
        } else if ($data === null || empty($data)){
            $this->error->ErrorAction();
        }
    }
}
