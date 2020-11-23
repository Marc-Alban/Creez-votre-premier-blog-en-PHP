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
        $this->commentManager = new CommentManager($this->commentRepository);
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
        $action = $this->get->getName('action') ?? null;

        switch ($page) {
            case 'home':
                //Route: index.php || localhost:8000/?page=home || localhost:8000/?page=home&action=logout || localhost:8000/?page=home&action=sendMessage
                //affiche la page d'accueil ou se déconnecte de la session utilisateur ou envoie un message
                switch ($action) {
                    case 'logout':
                        $this->frontUserController->logoutAction();
                    break;
                    case 'sendMessage':
                        $this->frontUserController->sendMailAction($this->mail);
                    break;
                }
                $this->frontUserController->homeAction();
            break;

            case 'posts':
                //Route: index.php || localhost:8000/?page=posts&perpage=1
                //affiche la liste de tous les articles
                !empty($perPage) ? $this->frontPostController->postsAction($perPage) : $this->frontPostController->postsAction($perPage = 1);
            break;

            case 'post':
                //Route: index.php || localhost:8000/?page=post&id=?
                //affiche 1 post + commentaires associés
                $this->frontPostController->postAction($this->userManager, $this->commentManager, $idGlobal);
            break;
            
            case 'register':
                //Route: index.php || localhost:8000/?page=register
                //Affiche la page d'inscription ou inscrit l'utilisateur
                ($action === 'registration') ? $this->frontUserController->registrationAction() : $this->frontUserController->registerAction();
            break;
            
            case 'login':
                //Route: index.php || localhost:8000/?page=login
                //Affiche la page de connection ou connecte l'utilisateur
                ($action === 'connection') ? $this->frontUserController->connectionAction() : $this->frontUserController->loginAction();
            break;

            case 'accountManagement':
                //Route: index.php || localhost:8000/?page=accountManagement
                //Affiche la page du compte utilisateur et peut modifié le compte
                ($action === 'sendDatasUser') ? $this->backUserController->updateUserAction() : $this->backUserController->accountManagementAction();
            break;
            
            case 'password':
                //Route: index.php || localhost:8000/?page=password
                //Affiche la page de modification de mot de passe ou le modifie
                ($action === 'modifPass') ?$this->backUserController->updatePasswordAction() : $this->backUserController->passwordAction();
            break;

            case 'password':
                //Route: index.php || localhost:8000/?page=password
                //Affiche la page de modification de mot de passe ou le modifie
                ($action === 'modifPass') ?$this->backUserController->updatePasswordAction() : $this->backUserController->passwordAction();
            break;

            case 'dashboard':
                //Route: index.php || localhost:8000/?page=dashboard
                //Affiche la page dashboard
                $this->backUserController->dashboardAction($this->commentManager, $this->postManager);
            break;
            
            case 'userManagement':
                //Route: index.php || localhost:8000/?page=usermanagement
                //Affiche la page role utilisateur et peut changer le rôle
                (!empty($perPage) && !empty($idGlobal) && ($action ==='admin' || $action ==='user'))?
                $this->backUserController->userManagementRoleAction($this->accessControl, $perPage):
                $this->backUserController->userManagementAction($perPage = 1);
            break;

            case 'allPosts':
                //Route: index.php || localhost:8000/?page=allPosts
                //Affiche la page tous les articles sur le backoffice peut aussi les supprimer
                if (!empty($perPage) && empty($idGlobal) &&$action === null) {
                    $this->backPostController->allPostsAction($perPage);
                    exit();
                }
                if (!empty($perPage) && !empty($idGlobal) && $action === 'delete') {
                    $this->backPostController->deletePostAction($idGlobal, $perPage);
                    exit();
                }
                $this->backPostController->allPostsAction($perPage = 1);
            break;

            case 'updatePost':
                //Route: index.php || localhost:8000/?page=updatePost
                //Affiche la page modification des articles
                (!empty($idGlobal) && $action ==='updatePostBdd')?
                $this->backPostController->updatePostBddAction($this->userRepository, $idGlobal):
                $this->backPostController->updatePostAction($idGlobal);
            break;

            case 'addPost':
                //Route: index.php || localhost:8000/?page=addPost
                //Affiche la page ajout d'un articles ou l'ajoute en database
                ($action ==='addPostAction')?
                $this->backPostController->addPostBddAction($this->userRepository):
                $this->backPostController->addPostAction();
            break;

            case 'allComments':
                //Route: index.php || localhost:8000/?page=allComments&perpage=?
                //Affiche la page tous les commentaires sur le backoffice peut aussi les supprimer ou les valider
                if (!empty($perPage) && empty($idGlobal) && $action === null) {
                    $this->backCommentController->allCommentsAction($perPage);
                    exit();
                }
                
                if ($page && !empty($idGlobal) && $action === 'valide') {
                    $this->backCommentController->valideCommentAction($idGlobal, $perPage);
                    exit();
                } elseif ($page && !empty($idGlobal) && $action === 'deleted') {
                    $this->backCommentController->deleteCommentAction($idGlobal, $perPage);
                    exit();
                }
                $this->backCommentController->allCommentsAction($perPage = 1);
            break;

            default:
                //Route: index.php || localhost:8000/?page=erro
                //On affiche une page d'erreur
                $this->errorController->notFound();
        }
    }
}
