<?php
declare(strict_types=1);
namespace  App\Service;

use App\View\View;
use App\Service\{Database,Mail};
use App\Controller\ErrorController;
use App\Service\Http\{Request,Session,Parameter};
use App\Service\Security\{AccessControl,Token};
use App\Controller\Frontoffice\{FrontCommentController,FrontPostController,FrontUserController};
use App\Controller\Backoffice\{BackCommentController,BackPostController,BackUserController};
use App\Model\Manager\{CommentManager,PostManager,UserManager};
use App\Model\Repository\{CommentRepository,PostRepository,UserRepository};

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
        $this->frontPostController = new FrontPostController($this->postManager, $this->view, $this->request);
        $this->frontUserController = new FrontUserController($this->userManager, $this->view, $this->token, $this->session, $this->request);
        //Controller - Back
        $this->backCommentController = new BackCommentController($this->commentManager, $this->view, $this->session, $this->request);
        $this->backPostController = new BackPostController($this->postManager, $this->view, $this->token, $this->session, $this->request);
        $this->backUserController = new BackUserController($this->userManager, $this->view, $this->token, $this->session, $this->request);
    }
    /**
     * Start the router with the correct route from the past url
     */
    public function run()
    {
        $page = $this->get->getName('page') ?? 'home';
        $perPage = $this->get->getName("perpage") ?? 1;
        $idGlobal = $this->get->getName("id") ?? null;
        $action = $this->get->getName('action') ?? null;

        switch ($page) {
            case 'home':
                //Route: index.php || localhost:8000/?page=home
                //affiche la page d'accueil
                $this->frontUserController->homeAction();
            break;

            case ('posts'):
                //Route: index.php || localhost:8000/?page=posts&perpage=1
                //affiche la liste de tous les articles
                isset($perPage) ? $this->frontPostController->postsAction((int) $perPage) : $this->postController->displayAllPosts((int) $perPage = 1);
            break;

            // case 'post':
            //     //Route: index.php || localhost:8000/?page=home
            //     //affiche 1 post + commentaires associés
            //     $this->postController->displayOneEpisode((int) $this->get['id']);
            // break;

            // case 'comment_error':
            //     //Route: index.php || localhost:8000/?page=home
            //     //affiche post + commentaire + msg erreur
            //     $this->postController->commentError((int) $this->get['id'], (string) $this->get['pseudo'], (string) $this->get['comment']);
            // break;

            // case 'save_com':
            //     //Route: index.php || localhost:8000/?page=home
            //     //sauvegarde du commentaire en passant au commentController les éléments du formulaires
            //     //$this->commentController->saveComment((int) $this->get['id'], $this->post['pseudo'], $this->post['comment']);
            //     $this->commentController->saveComment((int) $this->get['id'], $this->post);
            // break;

            // case 'signal':
            //     //Route: index.php || localhost:8000/?page=home
            //     //on prend en compte le signalement du commentaire
            //     $this->commentController->reportComment((int) $this->get['comment_id'], (int) $this->get['id']);
            // break;

            // case 'new_post':
            //     //Route: index.php || localhost:8000/?page=home
            //     //vers l'éditeur de texte
            //     $this->backPostController->addPost();
            // break;

            // case 'save_draft':
            //     //Route: index.php || localhost:8000/?page=home
            //     //Sauvegarder le brouillon
            //     $this->draftController->saveDraft((int) $this->post['episode'], (string) $this->post['title'], (string) $this->post['episode_text']);                
            // break;

            // case 'publish':
            //     //Route: index.php || localhost:8000/?page=home
            //     //Enregistrement du post dans la BDD
            //     $this->backPostController->savePost((int) $this->post['episode'], (string) $this->post['title'], (string) $this->post['episode_text'], $this->post['hidden_input']);
            // break;

            // case 'publish_draft':
            //     //Route: index.php || localhost:8000/?page=home
            //     //Changement de status de l'épisode, qui passe de brouillon à publié
            //     $this->draftController->publishDraft((int) $this->get['id'], $this->post);
            // break;

            // case 'drafts':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->draftController->displayDrafts();
            // break;

            // case 'update_draft':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->draftController->updateDraft((int) $this->get['episode']);
            // break;

            // case 'save_updated_draft':
            //     //route: index.php || localhost:8000/?page=home
            //     //on écrase l'ancien brouillon et enregistre le nouveau
            //     $this->draftController->saveAndOverwrite((int) $this->get['episode_id'], (int) $this->post['episode'], (string) $this->post['title'], (string) $this->post['episode_text']);
            // break;

            // case 'delete_draft' :
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->draftController->deleteDraft((int) $this->get['episode']);
            // break;

            // case 'episodes_list':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backPostController->getEpisodes();
            // break;

            // case 'update_post':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backPostController->updateEpisode((int) $this->get['post_id']);
            // break;

            // case 'save_updated_post':
            //     //Route: index.php/page=save_updated_post&episode_id
            //     $this->backPostController->overwritePost((int) $this->get['episode_id'], (int) $this->post['episode'], (string) $this->post['title'], (string) $this->post['episode_text'], $this->post['hidden_input']);
            // break;

            // case 'delete_post':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backPostController->deletePost((int) $this->get['post_id']);
            // break;

            // case 'reported_comments':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backCommentController->getReportedComments();
            // break;

            // case 'delete_reported_comment':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backCommentController->deleteReportedComment((int) $this->get['id']);
            // break;

            // case 'comments_list':
            //     //Route: index.php || localhost:8000/?page=home
            //     isset($this->get['page']) ? $this->backCommentController->getCommentsList((int) $this->get['page']) : $this->backCommentController->getCommentsList((int) $this->get['page'] = 1);
            // break;

            // case 'validate_comment':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backCommentController->validateComment((int) $this->get['id']);
            // break;

            // case 'delete_comment':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backCommentController->deleteComment((int) $this->get['id']);
            // break;
            
            // case 'get_form_data':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->backPostController->getPostData((int) $this->get["episode"], $this->get["title"], $this->get["episode_text"]);
            // break;

            // case 'get_draft_data':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->draftController->getDraftData((int) $this->get['episode'], $this->get['title'], $this->get['content']);
            // break;

            // case 'authentification':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->userController->authentification();
            // break;

            // case 'log_in':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->userController->logIn($this->post);
            // break;

            // case 'log_out':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->userController->logOut();
            // break;

            // case 'user_page':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->userController->userPage();
            // break;

            // case 'change_username':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->userController->changeUsername($this->post);
            // break;

            // case 'change_password':
            //     //Route: index.php || localhost:8000/?page=home
            //     $this->userController->changePassword($this->post);
            // break;

            default:
                //Route: index.php || localhost:8000/?page=home
                //On affiche une page d'erreur
                $this->errorController->notFound();
        }
    }
}
