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

/************************************Controller************************************************* */
    public function page(string $arg): string
    {
        $pathControllerFront = 'App\Controller\Frontoffice\\'.$this->pageMaj.'Controller';
        $pathControllerBack = 'App\Controller\Backoffice\\'.$this->pageMaj.'Controller';
        $pathManager = 'App\Model\Manager\\'.$this->pageMaj.'Manager';
        $path = ROOT.'src\Model\Repository\\'.$this->pageMaj.'Repository.php';

        $namePageRepo = [
            'Inscription' => 'User',
            'Connexion' => 'User',
        ];

        if(file_exists($path)){
            $pathRepository = $path;
        }else if(!file_exists($path)){
            if(array_key_exists($this->pageMaj, $namePageRepo)){
                $pathRepository = ROOT.'src\Model\Repository\\'.$namePageRepo[$this->pageMaj].'Repository.php';
            }else if(array_key_exists($this->pageMaj, $namePageRepo) === false ){
                $pathRepository = $path;
            }
        }

        if(in_array($this->pageMaj, $this->pageFront) || empty($this->pageMaj) || !in_array($this->pageMaj, $this->pageBack))
        {
            if($arg === 'controller'){
                return $pathControllerFront;
            }
        }
        else if(in_array($this->pageMaj, $this->pageBack))
        {
            if($arg === 'controller'){
                return $pathControllerBack;
            }
        }

        if($arg === 'manager'){
            return $pathManager;
        }else if($arg === 'repository'){
            return $pathRepository;
        }
    }
/************************************End Controller************************************************* */

/************************************Call Method With Controller************************************************* */
    public function call(array $datas): ?array
    {
        $controllerClass = $this->page('controller');
        $managerClass = $this->page('manager');
        $repoClass = $this->page('repository');
        
        if(file_exists($repoClass)){
            $pathApp = str_replace('src', 'App', $repoClass);
            $subPath = substr_replace($pathApp, '', 0, -39);
            $pathRepo = str_replace('.php', '', $subPath);
            $repo = new $pathRepo($this->database);
            $manager = new $managerClass($repo,['token' => $this->token, 'session' => $this->session]);
        }elseif(!file_exists($repoClass)){
            $manager = new $managerClass(['token' => $this->token, 'session' => $this->session]);
        }
        $controllerObject = new $controllerClass($manager, ['view' => $this->view, 'error' => $this->error, 'token' => $this->token, 'session' => $this->session]);
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
    }
/************************************End Return Error Action************************************************ */
}
