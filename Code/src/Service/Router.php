<?php
declare(strict_types=1);
namespace  App\Service;

use App\Controller\Frontoffice\PostController;
use App\Model\Manager\PostManager;
use App\Model\Repository\PostRepository;
use App\View\View;
use App\Service\Database;

final class Router
{
    private PostController $postController;
    private PostManager $postManager;
    private PostRepository $postRepo;
    private Database $db;
    private View $view;
    private array $get;
    public function __construct()
    {
        // dépendance
        $this->postRepo = new PostRepository($this->db);
        $this->postManager = new PostManager($this->postRepo);
        $this->view = new View();
        // injection des dépendances
        $this->postController = new PostController($this->postManager, $this->view);
        // En attendent de mettre en place la class App\Service\Http\Request --> gestion super global
        $this->get = $_GET;
    }

    public function run(): void
    {
        $action = isset($this->get['action']) && isset($this->get['id']) && $this->get['action'] === 'post';

        if ($action) {
            // route http://localhost:8000/?action=post&id=5

            $this->postController->displayOneAction((int)$this->get['id']);
        } elseif (!$action) {
            // faire un controller pour la gestion d'erreur
            echo "Error 404 - cette page n'existe pas<br><a href='http://localhost:8000/?action=post&id=5'>Ici</a>";
        }
    }
}
