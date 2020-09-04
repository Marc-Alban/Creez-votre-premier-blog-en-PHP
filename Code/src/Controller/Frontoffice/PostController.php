<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Controller\ErrorController;
use App\Model\Entity\Post;
use App\Model\Repository\Frontoffice\PostRepository;
use App\Model\Manager\Frontoffice\PostManager;
use App\Service\Database;
use App\Model\Repository\DatabaseProperties;
use App\View\View;

final class PostController
{
    private ErrorController $error;
    private DatabaseProperties $dbProperties;
    private Database $db;
    private PostManager $postManager;
    private PostRepository $postRepo;
    private View $view;

    public function __construct(PostManager $postManager, View $view)
    {
        $this->postManager = $postManager;
        $this->view = $view;
        // Dépendances
        $this->view = new View();
        $this->error = new ErrorController();
        $this->dbProperties = new DatabaseProperties();
        // Injection des dépendances
        $this->db = new Database($this->dbProperties);
        $this->postRepo = new PostRepository($this->db);
        $this->postManager = new PostManager($this->postRepo);
    }
    
    public function PostAction(array $datas): void
    {
        $data = $this->postManager->showOne($datas['get']['id']);
        if ($data instanceof Post) {
            $this->view->render('frontoffice', 'post', ["post" => $data]);
        } elseif ($data === null) {
        $this->error->ErrorAction();
        }
    }
}
