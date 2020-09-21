<?php
declare(strict_types=1);
namespace  App\Service;

use App\Service\ConfigProperties;
use App\Service\Http\Session;
use App\Service\Database;
use App\Service\Security\Token;
use App\View\View;
use App\Controller\ErrorController;

final class Router
{
    private ?string $action;
    private ?int $id;
    private string $page;
    private string $pageMaj;
    private ?array $pageFront;
    private ?array $pageBack;
    private ConfigProperties $configProperties;
    private Database $database;
    private Session $session;
    private Token $token;
    private View $view;
    private ?ErrorController $error;  


    public function __construct()
    {
        // dépendance
        $this->session = new Session();
        $this->view = new View($this->session);
        $this->token = new Token();
        $this->configProperties = new ConfigProperties();
        $this->database = new Database($this->configProperties);
        $this->error = new ErrorController($this->view);
        // En attendent de mettre en place la class App\Service\Http\Request --> gestion super global
        $idUrl = $_GET['id'] ?? null;
        $this->id = intval($idUrl);
        $this->action = $_GET['action'] ?? null;
        $this->page = $_GET['page'] ?? "Home";
        $this->pageMaj = ucfirst($this->page);
        $this->pageFront = ['Home','Post','Blog', 'Connexion', 'Inscription'];
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
        return $this->error->ErrorAction();
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
        return $this->error->ErrorAction();
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
        return $this->error->ErrorAction();
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
            $manager = new $managerClass($repo,$this->token);
        }elseif(!file_exists($pathVerif)){
            $manager = new $managerClass($this->token);
        }
        $controllerObject = new $controllerClass($manager, $this->view, $this->error, $this->token, $this->session);
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
                $this->call(['get'=>$_GET, 'post'=>$_POST, 'files'=>$_FILES, 'session'=>$this->session->getSession()]);
            }
            else if($this->action === null && $this->id !== null)
            {
                $this->call(['get'=>$_GET, 'post'=>$_POST, 'session'=>$this->session->getSession()]);
            }
        }
        else if(!in_array($this->pageMaj, $this->pageFront) || !in_array($this->pageMaj, $this->pageBack))
        {
            $this->error->ErrorAction();
        }
    }
/************************************End Start Routeur************************************************* */
/************************************Return Error Action************************************************* */
    public function error(): void
    {
        $this->error->ErrorAction();
        exit();
    }
/************************************End Return Error Action************************************************ */
}
