<?php
declare(strict_types=1);
namespace  App\Service;

use App\Controller\ErrorController;
use App\Model\Manager\CommentManager;
use App\Model\Manager\UserManager;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\UserRepository;
use App\Service\Database;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Mail;
use App\Service\Security\Token;
use App\View\View;

final class Router
{
    private ConfigProperties $configProperties;
    private Database $database;
    private Request $request;
    private Token $token;
    private Session $session;
    private View $view;
    private ErrorController $error;
    private Mail $mail;
    private $url = [];
    public function __construct()
    {
        $this->request = new Request();
        $this->token = new Token();
        $this->configProperties = new ConfigProperties();
        $this->database = new Database($this->configProperties);
        $this->session = new Session();
        $this->view = new View($this->session);
        $this->error = new ErrorController($this->view);
        $this->mail = new Mail($this->request, $this->view, $this->token, $this->session);
    }
    public function run(): void
    {
        $pageTab = $this->request->getServer() ?? 'home';
        $this->url = explode("/", $pageTab);
        $pageMin = lcfirst($this->url[1]);  
        $parameter = $this->url[2] ?? null;

        $userFrontPage = ['home','connexion','inscription'];
        $postFrontPage = ['post','posts'];
        $userBackPage = ['dashboard','password'];
        $postBackPage = ['allPost','addPost','updatePost'];
        $commentBackPage = ['allComments'];
        $params = ['signin', ''];

        $pathUserRepository = 'App\Model\Repository\UserRepository';
        $pathUserManager = 'App\Model\Manager\UserManager';
        $userRepository = new $pathUserRepository($this->database);
        $userManager = new $pathUserManager($userRepository);

        $pathPostRepository = 'App\Model\Repository\PostRepository';
        $pathPostManager = 'App\Model\Manager\PostManager';
        $postRepository = new $pathPostRepository($this->database);
        $postManager = new $pathPostManager($postRepository);

        $pathCommentRepository = 'App\Model\Repository\CommentRepository';
        $pathCommentManager = 'App\Model\Manager\CommentManager';
        $commentRepository = new $pathCommentRepository($this->database);
        $commentManager = new $pathCommentManager($commentRepository);

        if (in_array($pageMin, $userFrontPage, true)) {
            $pathController = 'App\Controller\Frontoffice\UserController';
            $instanceController = new $pathController($userManager, $this->view, $this->token, $this->session, $this->request);
        } elseif (in_array($pageMin, $postFrontPage, true)) {
            $pathController = 'App\Controller\Frontoffice\PostController';
            $instanceController = new $pathController($postManager, $this->view, $this->request, $this->token, $this->session);
        } elseif (in_array($pageMin, $userBackPage, true)) {
            $pathController = 'App\Controller\Backoffice\UserController';
            $instanceController = new $pathController($userManager, $this->view, $this->token, $this->session, $this->request);
        } elseif (in_array($pageMin, $postBackPage, true)) {
            $pathController = 'App\Controller\Backoffice\PostController';
            $instanceController = new $pathController($postManager, $this->view, $this->token, $this->session, $this->request);
        } elseif (in_array($pageMin, $commentBackPage, true)) {
            $pathController = 'App\Controller\Backoffice\CommentController';
            $instanceController = new $pathController($commentManager, $this->view, $this->token, $this->session, $this->request);
        } 
        
        if ($pageMin === 'post') {
            $commentRepo = new CommentRepository($this->database);
            $commentManager = new CommentManager($commentRepo);
            $userRepo = new UserRepository($this->database);
            $userManager = new UserManager($userRepo);
            $instanceController->postAction($commentManager, $userManager);
        } else if($pageMin !== 'post') {
            $methode = $pageMin.'Action';
            $instanceController->$methode();
        }
        
        if (!in_array($pageMin, $userFrontPage, true) || !in_array($pageMin, $postFrontPage, true) || !in_array($pageMin, $userBackPage, true) || !in_array($pageMin, $postBackPage, true) || !in_array($pageMin, $commentBackPage, true)) {
            $this->error->notFound();
        }

    }
}
