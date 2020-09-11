<?php
declare(strict_types=1);
namespace  App\Service;

use App\Controller\ErrorController;
use App\Service\ConfigProperties;
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
    private ConfigProperties $configProperties;
    private Database $database;
    private View $view;


    public function __construct()
    {
        // dépendance
        $this->errorAction = new ErrorController();
        $this->configProperties = new ConfigProperties();
        $this->view = new View();
        $this->database = new Database($this->configProperties);
        // En attendent de mettre en place la class App\Service\Http\Request --> gestion super global
        $idUrl = $_GET['id'] ?? null;
        $this->id = intval($idUrl);
        $this->action = $_GET['action'] ?? null;
        $this->page = $_GET['page'] ?? "Home";
        $this->pageMaj = ucfirst($this->page);
        $this->pageFront = ['Home','Post','Blog'];
        $this->pageBack = [];
    }

/************************************Controller************************************************* */
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
        return $this->error();
    }
/************************************End Controller************************************************* */
/************************************Manager Class************************************************* */
public function managerClass(): string
{  
    if(in_array($this->pageMaj, $this->pageFront) || !in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Manager\\'.$this->pageMaj.'Manager';
    }
    else if(in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Manager\\'.$this->pageMaj.'Manager';
    }
    return $this->error();
}
/************************************End Manager Class************************************************* */
/************************************Manager Class************************************************* */
public function repositoryClass(): string
{  
    if(in_array($this->pageMaj, $this->pageFront) || !in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Repository\\'.$this->pageMaj.'Repository';
    }
    else if(in_array($this->pageMaj, $this->pageBack))
    {
        return 'App\Model\Repository\\'.$this->pageMaj.'Repository';
    }
    return $this->error();
}
/************************************End Manager Class************************************************* */
/************************************Call Method With Controller************************************************* */
    public function call(array $datas): ?array
    {
        $controllerClass = $this->controller();
        $managerClass = $this->managerClass();
        $repoClass = $this->repositoryClass();
        $replacePath = str_replace("App","src",$repoClass);
        $pathVerif = ROOT.$replacePath.'.php';
        if(file_exists($pathVerif)){
            $repo = new $repoClass($this->database);
            $manager = new $managerClass($repo);
        }elseif(!file_exists($pathVerif)){
            $manager = new $managerClass();
        }
        $view = $this->view ;
        $controllerObject = new $controllerClass($manager, $view);
        $methode = $this->pageMaj.'Action';
        return $controllerObject->$methode($datas);
    }
/************************************End Call Methode With Controller************************************************* */
/************************************Start Router************************************************* */
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
    public function error(): void
    {
        $this->errorAction->ErrorAction();
        exit();
    }
/************************************End Return Error Action************************************************ */
}
