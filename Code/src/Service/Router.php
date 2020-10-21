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
    private $url;
    private $page;
    private $id;
    private $action;
    public function __construct()
    {
        $this->request = new Request();
        $this->token = new Token();
        $this->configProperties = new ConfigProperties();
        $this->database = new Database($this->configProperties);
        $this->session = new Session();
        $this->view = new View($this->session);
        $this->error = new ErrorController($this->view);
        $this->mail = new Mail($this->request, $this->view);
    }
    public function run()
    {
        $this->url = $this->request->getGet()->get('page') ?? 'home';
        $this->page = lcfirst($this->url);

        $this->id = $this->request->getGet()->get('id') ?? null;
        $this->action = $this->request->getGet()->get('action') ?? null;

        $userFrontPage = ['home','connexion','inscription'];
        $postFrontPage = ['post','posts'];
        $userBackPage = ['dashboard','password'];
        $postBackPage = ['allPost','addPost','updatePost'];
        $commentBackPage = ['allComments'];

        if (in_array($this->page, $userFrontPage, true)) {
            $pathController = 'App\Controller\Frontoffice\UserController';
            $pathUserRepository = 'App\Model\Repository\UserRepository';
            $pathUserManager = 'App\Model\Manager\UserManager';
            $userRepository = new $pathUserRepository($this->database);
            $userManager = new $pathUserManager($userRepository);
            $instanceController = new $pathController($userManager, $this->view, $this->token, $this->session, $this->request);
            if ($this->page === 'home' && $this->action === null) {
                $methode = $this->page .'Action';
                return $instanceController->$methode();
            } elseif ($this->page === 'home' && $this->action === 'sendMessage') {
                return $instanceController->sendMailAction($this->mail);
            }
        } elseif (in_array($this->page, $postFrontPage, true)) {
            $pathPostRepository = 'App\Model\Repository\PostRepository';
            $pathPostManager = 'App\Model\Manager\PostManager';
            $postRepository = new $pathPostRepository($this->database);
            $postManager = new $pathPostManager($postRepository);
            $pathController = 'App\Controller\Frontoffice\PostController';
            $instanceController = new $pathController($postManager, $this->view, $this->request, $this->token, $this->session);
            if ($this->page  === 'post' && $this->id) {
                $commentRepo = new CommentRepository($this->database);
                $commentManager = new CommentManager($commentRepo);
                $userRepo = new UserRepository($this->database);
                $userManager = new UserManager($userRepo);
                return $instanceController->postAction($commentManager, $userManager);
            } elseif ($this->page !== 'post') {
                $methode = $this->page .'Action';
                return $instanceController->$methode();
            }
            $this->error->notFound();
        } elseif (in_array($this->page, $userBackPage, true)) {
            $pathUserRepository = 'App\Model\Repository\UserRepository';
            $pathUserManager = 'App\Model\Manager\UserManager';
            $userRepository = new $pathUserRepository($this->database);
            $userManager = new $pathUserManager($userRepository);
            $pathController = 'App\Controller\Backoffice\UserController';
            $instanceController = new $pathController($userManager, $this->view, $this->token, $this->session, $this->request);
            $methode = $this->page .'Action';
            return $instanceController->$methode();
        } elseif (in_array($this->page, $postBackPage, true)) {
            $pathPostRepository = 'App\Model\Repository\PostRepository';
            $pathPostManager = 'App\Model\Manager\PostManager';
            $postRepository = new $pathPostRepository($this->database);
            $postManager = new $pathPostManager($postRepository);
            $pathController = 'App\Controller\Backoffice\PostController';
            $instanceController = new $pathController($postManager, $this->view, $this->token, $this->session, $this->request);
            $methode = $this->page .'Action';
            return $instanceController->$methode();
        } elseif (in_array($this->page, $commentBackPage, true)) {
            $pathCommentRepository = 'App\Model\Repository\CommentRepository';
            $pathCommentManager = 'App\Model\Manager\CommentManager';
            $commentRepository = new $pathCommentRepository($this->database);
            $commentManager = new $pathCommentManager($commentRepository);
            $pathController = 'App\Controller\Backoffice\CommentController';
            $instanceController = new $pathController($commentManager, $this->view, $this->session, $this->request);
            $methode = $this->page .'Action';
            return $instanceController->$methode();
        } elseif (!in_array($this->page, $userFrontPage, true) || !in_array($this->page, $postFrontPage, true) || !in_array($this->page, $userBackPage, true) || !in_array($this->page, $postBackPage, true) || !in_array($this->page, $commentBackPage, true)) {
            $this->error->notFound();
        }
    }
}
