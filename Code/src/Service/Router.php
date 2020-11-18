<?php
declare(strict_types=1);
namespace  App\Service;

use App\Controller\ErrorController;
use App\Controller\Frontoffice\CommentController;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Model\Repository\UserRepository;
use App\Service\Database;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Mail;
use App\Service\Security\AccessControl;
use App\Service\Security\Token;
use App\View\View;

final class Router
{
    private AccessControl $accessControl;
    private ConfigProperties $configProperties;
    private Database $database;
    private Request $request;
    private Token $token;
    private Session $session;
    private View $view;
    private ErrorController $error;
    private Mail $mail;
    private ?string $url;
    private ?string $page;
    private ?string $idGlobal;
    private ?string $action;
    public function __construct()
    {
        $this->accessControl = new AccessControl();
        $this->session = new Session();
        $this->request = new Request();
        $this->token = new Token();
        $this->configProperties = new ConfigProperties();
        $this->database = new Database($this->configProperties);
        $this->view = new View($this->session);
        $this->error = new ErrorController($this->view);
        $this->mail = new Mail($this->request, $this->view);
    }
    /**
     * Start the router with the correct route from the past url
     *
     */
    public function run()
    {
        $this->url = $this->request->getGet()->get('page') ?? 'home';
        $this->page = lcfirst($this->url);
        $this->idGlobal = $this->request->getGet()->get('id') ?? "0";
        $this->action = $this->request->getGet()->get('action') ?? null;
        $userFrontPage = ['home','login','register'];
        $postFrontPage = ['post','posts'];
        $userBackPage = ['accountManagement','dashboard','password','userManagement'];
        $postBackPage = ['allPosts','addPost','updatePost'];
        $commentBackPage = ['allComments'];
        if (in_array($this->page, $userFrontPage, true)) {
            $pathController = 'App\Controller\Frontoffice\UserController';
            $pathUserRepository = 'App\Model\Repository\UserRepository';
            $pathUserManager = 'App\Model\Manager\UserManager';
            $userRepository = new $pathUserRepository($this->database);
            $userManager = new $pathUserManager($userRepository, $this->accessControl, $this->session);
            $instanceController = new $pathController($userManager, $this->view, $this->token, $this->session, $this->request);
            if ($this->page === 'home' && $this->action === 'sendMessage') {
                return $instanceController->sendMailAction($this->mail);
            } elseif ($this->page === 'home' && $this->action === 'logout') {
                return $instanceController->logoutAction();
            } elseif ($this->page === 'register' && $this->action === 'registration') {
                return $instanceController->registrationAction();
            } elseif ($this->page === 'login' && $this->action === 'connection') {
                return $instanceController->connectionAction();
            } elseif ($this->action === null) {
                $methode = $this->page .'Action';
                return $instanceController->$methode();
            }
        } elseif (in_array($this->page, $postFrontPage, true)) {
            $pathPostRepository = 'App\Model\Repository\PostRepository';
            $pathPostManager = 'App\Model\Manager\PostManager';
            $postRepository = new $pathPostRepository($this->database);
            $postManager = new $pathPostManager($postRepository);
            $pathController = 'App\Controller\Frontoffice\PostController';
            $instanceController = new $pathController($postManager, $this->view, $this->request);
            if ($this->page  === 'post' && $this->idGlobal && $this->action === null) {
                $commentRepo = new CommentRepository($this->database);
                $commentManager = new CommentManager($commentRepo);
                $commentController =  new CommentController($commentManager, $this->request, $this->token, $this->session);
                $userRepo = new UserRepository($this->database);
                $userManager = new UserManager($userRepo, $this->accessControl, $this->session);
                $comments = $commentController->findAllPostCommentsAction();
                return $instanceController->postAction($userManager, $comments, null);
            } elseif ($this->page  === 'post' && $this->idGlobal && $this->action === 'sendComment') {
                $commentRepo = new CommentRepository($this->database);
                $commentManager = new CommentManager($commentRepo);
                $commentController =  new CommentController($commentManager, $this->request, $this->token, $this->session);
                $userRepo = new UserRepository($this->database);
                $userManager = new UserManager($userRepo, $this->accessControl, $this->session);
                $message = $commentController->sendAction($userManager);
                $comments = $commentController->findAllPostCommentsAction();
                return $instanceController->postAction($userManager, $comments, $message);
            } elseif ($this->page === 'post' && $this->idGlobal === "0" || $this->page === 'post' && empty($this->global)) {
                return $instanceController->postAction(null, null, null);
            }
            $methode = $this->page .'Action';
            return $instanceController->$methode();
        } elseif (in_array($this->page, $userBackPage, true)) {
            $pathUserRepository = 'App\Model\Repository\UserRepository';
            $pathUserManager = 'App\Model\Manager\UserManager';
            $userRepository = new $pathUserRepository($this->database);
            $userManager = new $pathUserManager($userRepository, $this->accessControl, $this->session);
            $pathController = 'App\Controller\Backoffice\UserController';
            $instanceController = new $pathController($userManager, $this->view, $this->token, $this->session, $this->request);
            if ($this->page === 'accountManagement' && $this->action === 'sendDatasUser') {
                return $instanceController->updateUserAction();
            } elseif ($this->page === 'password' && $this->action === 'modifPass') {
                return $instanceController->updatePasswordAction();
            } elseif ($this->page === 'dashboard') {
                $postRepo = new PostRepository($this->database);
                $postManager = new PostManager($postRepo);
                $commentRepo = new CommentRepository($this->database);
                $commentManager = new CommentManager($commentRepo);
                return $instanceController->dashboardAction($commentManager, $postManager);
            } elseif (($this->page === 'userManagement' && $this->action === 'user') || ($this->page === 'userManagement' && $this->action === 'admin')) {
                return $instanceController->userManagementRoleAction();
            }
            $methode = $this->page .'Action';
            return $instanceController->$methode();
        } elseif (in_array($this->page, $postBackPage, true)) {
            $pathPostRepository = 'App\Model\Repository\PostRepository';
            $pathPostManager = 'App\Model\Manager\PostManager';
            $postRepository = new $pathPostRepository($this->database);
            $postManager = new $pathPostManager($postRepository);
            $pathController = 'App\Controller\Backoffice\PostController';
            $instanceController = new $pathController($postManager, $this->view, $this->token, $this->session, $this->request);
            if ($this->page === 'addPost' && $this->action === 'addPostAction') {
                $userRepo = new UserRepository($this->database);
                return $instanceController->addPostDashboardAction($userRepo);
            } elseif ($this->page === 'updatePost' && $this->action === 'updatePostBdd' && $this->idGlobal) {
                $userRepo = new UserRepository($this->database);
                return $instanceController->updatePostBddAction($userRepo);
            } elseif ($this->page === 'allPosts' && $this->action === 'delete' && $this->idGlobal) {
                return $instanceController->deletePostAction();
            }
            $methode = $this->page .'Action';
            return $instanceController->$methode();
        } elseif (in_array($this->page, $commentBackPage, true)) {
            $pathCommentRepo = 'App\Model\Repository\CommentRepository';
            $pathCommentManager = 'App\Model\Manager\CommentManager';
            $commentRepository = new $pathCommentRepo($this->database);
            $commentManager = new $pathCommentManager($commentRepository);
            $pathController = 'App\Controller\Backoffice\CommentController';
            $instanceController = new $pathController($commentManager, $this->view, $this->session, $this->request);
            if ($this->page === 'allComments' && $this->action === 'valide') {
                return $instanceController->valideCommentAction();
            } elseif ($this->page === 'allComments' && $this->action === 'deleted') {
                return $instanceController->deleteCommentAction();
            }
            $methode = $this->page .'Action';
            return $instanceController->$methode();
        } elseif (!in_array($this->page, $userFrontPage, true) || !in_array($this->page, $postFrontPage, true) || !in_array($this->page, $userBackPage, true) || !in_array($this->page, $postBackPage, true) || !in_array($this->page, $commentBackPage, true)) {
            $this->error->notFound();
        }
    }
}
