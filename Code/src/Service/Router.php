<?php
declare(strict_types=1);
namespace  App\Service;

use App\Controller\ErrorController;
use App\Model\Repository\DatabaseProperties;
use App\Service\Database;
use App\View\View;

final class Router
{
    private ?string $action;
    private ErrorController $errorAction;
    private ?int $id;
    private string $page;
    private string $pageMaj;
    private ?array $pageFront;
    private ?array $pageBack;
    private DatabaseProperties $databaseProperties;
    private Database $database;
    private View $view;


    public function __construct()
    {
        // dépendance
        $this->errorAction = new ErrorController();
        $this->errorAction = new ErrorController();
        $this->view = new View();
        //Injection de dépendence
        $this->databaseProperties = new DatabaseProperties();
        $this->database = new Database($this->databaseProperties);
        // En attendent de mettre en place la class App\Service\Http\Request --> gestion super global
        $idUrl = $_GET['id'] ?? null;
        $this->id = intval($idUrl);
        $this->action = $_GET['action'] ?? null;
        $this->page = $_GET['page'] ?? $this->error();
        $this->pageMaj = ucfirst($this->page);
        $this->pageFront = ['Home','Post'];
        $this->pageBack = [];
    }

/************************************Controller************************************************* */
    /**
     * 1) Renvoie le bon controller en fonction de la page qui est passé dans l'url
     *
     * @return string
     */
    public function controller(): string
    {
        if(in_array($this->pageMaj, $this->pageFront) || empty($this->pageMaj) || !in_array($this->pageMaj, $this->pageBack))
        {
            return 'App\Controller\Frontoffice\\'.$this->pageMaj.'Controller';
        }
        else if(in_array($this->pageMaj, $this->pageBack))
        {
            return 'App\Controller\Backoffice\\'.$this->pageMaj.'Controller';
        }
    }
/************************************End Controller************************************************* */
/************************************Manager Class************************************************* */
public function managerClass()
{  
    if(in_array($this->pageMaj, $this->pageFront) || !in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Manager\Frontoffice\\'.$this->pageMaj.'Manager';
    }
    else if(in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Manager\Backoffice\\'.$this->pageMaj.'Manager';
    }
}
/************************************End Manager Class************************************************* */
/************************************Manager Class************************************************* */
public function repositoryClass()
{  
    if(in_array($this->pageMaj, $this->pageFront) || !in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Repository\Frontoffice\\'.$this->pageMaj.'Repository';
    }
    else if(in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Repository\Backoffice\\'.$this->pageMaj.'Repository';
    }
}
/************************************End Manager Class************************************************* */
/************************************Call Method With Controller************************************************* */
    /**
     * 1) Appel du controller 
     * 2) Instance de ce dernier 
     * 3) Appel de la methode avec le nom de la page suivi du mot Action
     * 4) Retourne sous-forme d'array l'objet et la méthode utilisée.
     *  
     * @return array
     */
    public function call(array $datas): ?array
    {
        $controllerClass = $this->controller();
        $managerClass = $this->managerClass();
        $repoClass = $this->repositoryClass();
        $repo = new $repoClass($this->database);
        $manager = new $managerClass($repo);
        $view = $this->view ;
        $controllerObject = new $controllerClass($manager,$view);
        $methode = $this->pageMaj.'Action';
        return $controllerObject->$methode($datas);
    }
/************************************End Call Methode With Controller************************************************* */
/************************************Start Router************************************************* */
    /**
     * 1)Vérification des paramètres dans l'url
     * 2)insertion des superglobales dans les futurs méthodes utilisées
     * si les mots action et id sont present ou pas  ou seulment action sans id ou encore aucun des deux.
     * 3)insertion des superglobales dans les futurs méthodes utilisées
     *si le mot action est absent et le mot id est present alors insertions des superglobale mais sans le file.active
     * 4)Une fois cette étape faite si rien n'est trouvé alors on affiche une erreur 404.
     */
    public function start(): void
    {
        if(in_array($this->pageMaj, $this->pageFront) || in_array($this->pageMaj, $this->pageBack))
        {
            if(($this->action === null && $this->id === null) || ($this->action !== null && $this->id !== null) || ($this->action !== null && $this->id === null))
            {
                $this->call(['get'=>$_GET, 'post'=>$_POST, 'files'=>$_FILES, 'session'=>$_SESSION]);
            }
            else if($this->action === null && $this->id !== null)
            {
                $this->call(['get'=>$_GET, 'post'=>$_POST, 'session'=>$_SESSION]);
            }
        }
        else if(!in_array($this->pageMaj, $this->pageFront) || !in_array($this->pageMaj, $this->pageBack))
        {
            $this->error();
        }
    }
/************************************End Start Routeur************************************************* */
/************************************Return Error Action************************************************* */
    /**
     * Renvoie la page 404 si rien n'est trouvée !
     *
     * @return void
     */
    public function error(): void
    {
        $this->errorAction->ErrorAction();
        exit();
    }
/************************************End Return Error Action************************************************ */
}
