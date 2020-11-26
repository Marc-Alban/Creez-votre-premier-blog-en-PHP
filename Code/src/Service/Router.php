<?php
declare(strict_types=1);
namespace  App\Service;

use App\Controller\Backoffice\BackCommentController;
use App\Controller\Backoffice\BackPostController;
use App\Controller\Backoffice\BackUserController;
use App\Controller\ErrorController;
use App\Controller\Frontoffice\FrontCommentController;
use App\Controller\Frontoffice\FrontPostController;
use App\Controller\Frontoffice\FrontUserController;
use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Model\Repository\CommentRepository;
use App\Model\Repository\PostRepository;
use App\Model\Repository\UserRepository;
use App\Service\Database;
use App\Service\Http\Parameter;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Mail;
use App\Service\Security\AccessControl;
use App\Service\Security\Token;
use App\View\View;

final class Router
{
    //Config & Database
    private ConfigProperties $configProperties;
    private Database $database;
    //Session
    private Session $session;
    //AccessControl
    private AccessControl $accessControl;
    //Request / -get - post
    private Request $request;
    private Parameter $post;
    private Parameter $get;
    //Token
    private Token $token;
    //View
    private View $view;
    //Mail
    private Mail $mail;
    //Controller - Error
    private ErrorController $errorController;
    //Controller - Front
    private FrontCommentController $frontCommentController;
    private FrontPostController $frontPostController;
    private FrontUserController $frontUserController;
    //Controller - Back
    private BackCommentController $backCommentController;
    private BackPostController $backPostController;
    private BackUserController $backUserController;
    //Manager
    private CommentManager $commentManager;
    private PostManager $postManager;
    private UserManager $userManager;
    //Repository
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        //Config & Database
        $this->configProperties = new ConfigProperties();
        $this->database = new Database($this->configProperties);
        //Session
        $this->session = new Session();
        //AccessControl
        $this->accessControl = new AccessControl();
        //Request / -get - post
        $this->request = new Request();
        $this->get = $this->request->getGet();
        $this->post = $this->request->getPost();
        //Token
        $this->token = new Token();
        //View
        $this->view = new View($this->session);
        //Mail
        $this->mail = new Mail($this->request, $this->view);
        //Repository
        $this->commentRepository = new CommentRepository($this->database);
        $this->postRepository = new PostRepository($this->database);
        $this->userRepository = new UserRepository($this->database);
        //Manager
        $this->commentManager = new CommentManager($this->commentRepository, $this->userRepository);
        $this->postManager = new PostManager($this->postRepository);
        $this->userManager = new UserManager($this->userRepository, $this->accessControl, $this->session);
        //Controller - Error
        $this->errorController = new ErrorController($this->view);
        //Controller - Front
        $this->frontCommentController = new FrontCommentController($this->commentManager, $this->request, $this->token, $this->session);
        $this->frontPostController = new FrontPostController($this->postManager, $this->view);
        $this->frontUserController = new FrontUserController($this->userManager, $this->view, $this->token, $this->session, $this->request);
        //Controller - Back
        $this->backCommentController = new BackCommentController($this->commentManager, $this->view, $this->session);
        $this->backPostController = new BackPostController($this->postManager, $this->view, $this->token, $this->session, $this->request);
        $this->backUserController = new BackUserController($this->userManager, $this->view, $this->token, $this->session, $this->request);
    }
    /**
     * Start the router with the correct route from the past url
     */
    public function run(): void
    {
        $page = $this->get->getName('page') ?? 'home';
        $perPage = (int) $this->get->getName("perpage") ?? 1;
        $idGlobal = (int) $this->get->getName("id") ?? null;
        $action = $this->get->getName("action") ?? null;

        switch ($page) {
// Front --------------------------------
            case 'home':
                //Route: index.php localhost:8000/?page=home
                $this->frontUserController->homeAction();
            break;

            case 'sendMail':
                //Route: index.php localhost:8000/?page=sendMail
                $this->frontUserController->sendMailAction($this->mail);
            break;

            case 'blog':
                //Route: index.php localhost:8000/?page=blog&perpage=1
                $this->frontPostController->blogAction($perPage);
            break;

            case 'post':
                //Route: index.php localhost:8000/?page=post&id=?
                $this->frontPostController->postAction($this->userManager, $this->commentManager, $this->session, $this->get, $idGlobal);
            break;

            case 'sendComment':
                //Route: index.php localhost:8000/?page=sendComment
                $this->frontCommentController->sendCommentAction($idGlobal);
            break;
// Front --------------------------------
// User Connection --------------------------------
            case 'register':
                //Route: index.php localhost:8000/?page=register
                $this->frontUserController->registerAction();
            break;

            case 'registerUser':
                //Route: index.php localhost:8000/?page=registerUser
                $this->frontUserController->registerUserAction();
            break;

            case 'login':
                //Route: index.php localhost:8000/?page=login
                $this->frontUserController->loginAction();
            break;

            case 'loginUser':
                //Route: index.php localhost:8000/?page=loginUser
                $this->frontUserController->loginUserAction();
            break;

            //Route: index.php localhost:8000/?page=logout
            case 'logout':
                $this->frontUserController->logoutAction();
            break;
// User Connection --------------------------------
// User Back --------------------------------
            case 'managementAccount':
                //Route: index.php localhost:8000/?page=managementAccount
                $this->backUserController->managementAccountAction();
            break;

            case 'managementUpdateAccount':
                //Route: index.php localhost:8000/?page=managementUpdateAccount
                $this->backUserController->managementUpdateAccountAction();
            break;
            
            case 'password':
                //Route: index.php localhost:8000/?page=password
                $this->backUserController->passwordAction();
            break;

            case 'passwordUpdate':
                //Route: index.php localhost:8000/?page=passwordUpdate
                $this->backUserController->passwordUpdateAction();
            break;

            case 'dashboard':
                //Route: index.php localhost:8000/?page=dashboard
                $this->backUserController->dashboardAction($this->commentManager, $this->postManager);
            break;
            
            case 'userManagement':
                //Route: index.php localhost:8000/?page=userManagement
                $this->backUserController->userManagementAction($perPage);
            break;

            case 'userManagementRole':
                //Route: index.php localhost:8000/?page=userManagementRole
                $this->backUserController->userManagementRoleAction($this->accessControl, $idGlobal, $perPage, $action);
            break;
// User Back --------------------------------
// Post Back --------------------------------
            case 'allPostsBack':
                //Route: index.php localhost:8000/?page=allPostsBack&perpage=?
                $this->backPostController->allPostsBackAction($perPage);
            break;

            case 'deletePostBack':
               //Route: index.php localhost:8000/?page=deletePostBack&perpage=?&id=?
                $this->backPostController->deletePostBackAction($idGlobal, $perPage);
            break;

            case 'updatePostBack':
                //Route: index.php localhost:8000/?page=updatePostBack&id=?
                $this->backPostController->updatePostBackAction($idGlobal);
            break;

            case 'updatePostBddBack':
                //Route: index.php localhost:8000/?page=updatePostBddBack&id=?
                $this->backPostController->updatePostBddBackAction($this->userRepository, $idGlobal);
            break;

            case 'addPostBack':
                //Route: index.php localhost:8000/?page=addPostBack
                $this->backPostController->addPostBackAction();
            break;

            case 'addPostBddBack':
                //Route: index.php localhost:8000/?page=addPostBddBack
                $this->backPostController->addPostBddBackAction($this->userRepository);
            break;
// Post Back --------------------------------
// Comment Back --------------------------------
            case 'allCommentsBack':
                //Route: index.php localhost:8000/?page=allCommentsBack&perpage=?
                $this->backCommentController->allCommentsBackAction($perPage);
            break;

            case 'deleteCommentBack':
                //Route: index.php localhost:8000/?page=deleteCommentBack
                $this->backCommentController->deleteCommentBackAction($idGlobal, $perPage);
            break;

            case 'valideCommentBack':
                //Route: index.php localhost:8000/?page=valideCommentBack
                $this->backCommentController->valideCommentBackAction($idGlobal, $perPage);
            break;
// Comment Back --------------------------------
// Default Page --------------------------------
            default:
                //Route: index.php localhost:8000/?page=erro
                //On affiche une page d'erreur
                $this->errorController->notFound();
// Default Page --------------------------------
        }
    }
}
