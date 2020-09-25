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
    private ?ErrorController $error;  
    private View $view;


    public function __construct()
    {
        // dépendance
        $this->session = new Session();
        $this->token = new Token();
        $this->configProperties = new ConfigProperties();
        $this->database = new Database($this->configProperties);
        $this->view = new View($this->session);
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
/**
 * Methode return the path of file needed;
 *
 * @param string $arg
 * @return string
 */
/************************************Controller************************************************* */
    public function pagePath(string $arg): string
    {
        //Path controller
        $pathControllerFront = 'App\Controller\Frontoffice\\'.$this->pageMaj.'Controller';
        $pathControllerBack = 'App\Controller\Backoffice\\'.$this->pageMaj.'Controller';
        if($arg === 'controller'){
            if(in_array($this->pageMaj, $this->pageFront) || empty($this->pageMaj) || !in_array($this->pageMaj, $this->pageBack))
            {
                return $pathControllerFront;
            }
            else if(in_array($this->pageMaj, $this->pageBack))
            {
                return $pathControllerBack;
            }
        //Path Manager
        }else if($arg === 'manager'){
            return 'App\Model\Manager\\'.$this->pageMaj.'Manager';
        //Path Repository
        }else if($arg === 'repository'){
        $path = ROOT.'src\Model\Repository\\'.$this->pageMaj.'Repository.php';
        $namePageRepo = [
            'Inscription' => 'User',
            'Connexion' => 'User',
        ];
            if(file_exists($path)){
                return $path;
            }else if(!file_exists($path)){
                return ROOT.'src\Model\Repository\\'.$namePageRepo[$this->pageMaj].'Repository.php';
            }
        }
    }
/************************************End Controller************************************************* */
/**
 * Method call the good controller,manager and repository
 *
 * @param array $datas
 * @return array|null
 */
/************************************Call Method With Controller************************************************* */
    public function call(array $datas): ?array
    {
        //paths
        $controllerClass = $this->pagePath('controller');
        $managerClass = $this->pagePath('manager');
        $repoClass = $this->pagePath('repository');
        //If the reposritory file exists
        if(file_exists($repoClass)){
            //Transform path into App instead of src and .php
            $pathApp = str_replace('src', 'App', $repoClass);
            $subPath = substr_replace($pathApp, '', 0, -39);
            $pathRepo = str_replace('.php', '', $subPath);
            //Repository
            $addRepo = null;
            $repoPage = $this->pageMaj . 'Repository';
            $repoTab = [
                'PostRepository' => 'UserRepository'
            ];
            if(array_key_exists($repoPage, $repoTab)){
                $addRepo = 'App\Model\Repository\\'.$repoTab[$repoPage];
                $addRepoInstance = new $addRepo($this->database);
            }else if(!array_key_exists($repoPage, $repoTab)){
                $addRepo = 'App\Model\Repository\\'.$repoPage;
                $addRepoInstance = new $pathRepo($this->database);
            }
            //Manager
            $addManager = null;
            $managerPage = $this->pageMaj . 'Manager';
            $managerTab = [
                'PostManager' => 'UserManager'
            ];
            if(array_key_exists($managerPage, $managerTab)){
                $addManager = 'App\Model\Manager\\'.$managerTab[$managerPage];
                $addManagerInstance = new $addManager(['addRepoInstance' => $addRepoInstance]);
            }else if(!array_key_exists($managerPage, $managerTab)){
                $addManager = 'App\Model\Repository\\'.$managerPage;
                $addManagerInstance = new $managerClass(['addRepoInstance' => $addRepoInstance, 'token' => $this->token, 'session' => $this->session]);
            }
            //Controller
            $controller = new $controllerClass(['manager' => $addManagerInstance ,'view' => $this->view, 'error' => $this->error, 'token' => $this->token, 'session' => $this->session]);
        }elseif(!file_exists($repoClass)){
            //manager
            $managerInstance = new $managerClass(['token' => $this->token, 'session' => $this->session]);
            //controller
            $controller = new $controllerClass(['managerInstance' => $managerInstance,'view' => $this->view, 'error' => $this->error, 'token' => $this->token, 'session' => $this->session]);
        }
        $methode = $this->pageMaj.'Action';
        return $controller->$methode($datas);
    }
/************************************End Call Methode With Controller************************************************* */
/**
 * Methode for start the router
 *
 * @return void
 */
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
    }
/************************************End Return Error Action************************************************ */
}
