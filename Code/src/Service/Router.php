<?php
declare(strict_types=1);
namespace  App\Service;

use App\Service\Database;
use App\Model\Repository\DatabaseProperties;
use App\Model\Manager\PostManager;
use App\Model\Repository\PostRepository;
use App\Controller\Frontoffice\PostController;
use App\View\View;

final class Router
{
    private PostController $postController;
    private PostManager $postManager;
    private PostRepository $postRepo;
    private Database $db;
    private DatabaseProperties $dataInfo;
    private View $view;
    private array $get;

    public function __construct()
    {
        // dépendance
        $this->dataInfo = new DatabaseProperties;
        $this->view = new View();
        // injection des dépendances
        $this->db = new Database($this->dataInfo);
        $this->postRepo = new PostRepository($this->db);
        $this->postManager = new PostManager($this->postRepo);
        $this->postController = new PostController($this->postManager, $this->view);
        // En attendent de mettre en place la class App\Service\Http\Request --> gestion super global
        $this->get = $_GET;
    }

    public function run(): void
    {
        $action = isset($this->get['action']) && isset($this->get['id']) && $this->get['action'] === 'post';

        if ($action) {
            $this->postController->displayOneAction((int)$this->get['id']);
        } elseif (!$action) {
            // faire un controller pour la gestion d'erreur
            echo "Error 404 - cette page n'existe pas<br><a href='http://localhost:8000/?action=post&id=5'>Ici</a>";
        }
    }
}
