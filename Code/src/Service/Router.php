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
        $this->pageFront = ['Home','Post','Blog', 'Connexion', 'Inscription', 'Deconnexion', 'User'];
        $this->pageBack = ['Dashboard','AllPost','AllComments','AddPost','UpdatePost','Password'];
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
        }
        //Path Manager
        if($arg === 'manager'){
            return 'App\Model\Manager\\'.$this->pageMaj.'Manager';
        }
        //Path Repository
        if($arg === 'repository'){
        $path = ROOT.'src\Model\Repository\\'.$this->pageMaj.'Repository.php';
        $namePageRepo = [
            'Inscription' => 'User',
            'Connexion' => 'User',
        ];
            if(!file_exists($path)){
                if(array_key_exists($this->pageMaj, $namePageRepo)){
                    return ROOT.'src\Model\Repository\\'.$namePageRepo[$this->pageMaj].'Repository.php';
                }
            }
        return $path;
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
            //--------------------------------Repository--------------------------------------------------//
            $addRepo = null;
            $addRepoPage = null;
            $repoPage = $this->pageMaj . 'Repository';
            $repoTab = [
                'ConnexionRepository' => 'UserRepository',
                'InscriptionRepository' => 'UserRepository',
                'PostRepository' => 'UserRepository',
                'DashboardRepository' => 'UserRepository',
                'PasswordRepository' => 'UserRepository',
                'AllCommentsRepository' => 'CommentRepository',
                'AddPostRepository' => 'PostRepository',
                'AllPostRepository' => 'blogRepository',
            ];
            if(array_key_exists($repoPage, $repoTab)){
                $addRepo = 'App\Model\Repository\\'.$repoTab[$repoPage];
                $repo = new $addRepo($this->database);
                $addPage = 'App\Model\Repository\\'.$this->pageMaj.'Repository';
                $addRepoPage = new $addPage($this->database);
            }else if(!array_key_exists($repoPage, $repoTab)){
                $addRepo = 'App\Model\Repository\\'.$this->pageMaj.'Repository';
                $repo = new $addRepo($this->database);
            }
            //--------------------------------Manager--------------------------------------------------//
            $addManager = null;
            $addManagerInstance = null;
            $managerPage = $this->pageMaj . 'Manager';
            $managerTab = [
                'PostManager' => 'UserManager',
                'AllPostManager' => 'BlogManager',
            ];
            if(array_key_exists($managerPage, $managerTab)){
                $addManager = 'App\Model\Manager\\'.$managerTab[$managerPage];
                $addManagerInstance = new $addManager(['repository' => ['repoAdd'=>$repo,'repoPage'=>$addRepoPage]]);
                $addPath = 'App\Model\Manager\\'.$managerPage;
                $managerPage = new $addPath(['repository' => ['repoAdd'=>$repo,'repoPage'=>$addRepoPage], 'token' => $this->token, 'session' => $this->session]);
            }else if(!array_key_exists($managerPage, $managerTab)){
                $addManager = 'App\Model\Manager\\'.$managerPage;
                $managerPage = new $addManager(['repository' => ['repoAdd'=>$repo,'repoPage'=>$addRepoPage], 'token' => $this->token, 'session' => $this->session]);
            }
            //--------------------------------Controller--------------------------------------------------//
            $controller = new $controllerClass(['manager' => ['managerAdd' => $addManagerInstance, 'managerPage' => $managerPage],'view' => $this->view, 'error' => $this->error, 'token' => $this->token, 'session' => $this->session]);
        }elseif(!file_exists($repoClass)){
            //manager
            $managerInstance = new $managerClass(['token' => $this->token, 'session' => $this->session]);
            //controller
            $controller = new $controllerClass(['manager' => $managerInstance,'view' => $this->view, 'error' => $this->error, 'token' => $this->token, 'session' => $this->session]);
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
