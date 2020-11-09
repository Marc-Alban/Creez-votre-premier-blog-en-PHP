<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\PostManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class PostController
{
    private PostManager $postManager;
    private View $view;
    private Token $token;
    private Session $session;
    private Request $request;
    private ?string $userSession;
    public function __construct(PostManager $postManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->postManager = $postManager;
        $this->request = $request;
        $this->userSession = $this->session->getSessionName('user') ?? null;
    }
    /**
     * display addPost Page
     *
     * @return void
     */
    public function addPostAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'addPost', []);
    }
    /**
     * method to add a post in bdd
     *
     * @return void
     */
    public function addPostDashboardAction(): void
    {
        $valdel = null;
        $title = null;
        $chapo = null;
        $description = null;
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $valdel = $this->postManager->checkFormAddPost($this->session, $this->token, $this->request);
        if($this->request->getPost() && $valdel !== ['sendPost']){
            $post = $this->request->getPost();
            $title = $post->get('title');
            $chapo = $post->get('chapo');
            $description = $post->get('description');
        }
        $this->view->render('backoffice', 'addPost', ["valdel" => $valdel, "title"=>$title,"chapo"=>$chapo,"description"=>$description]);
    }
    /**
     * Display allPostPage
     *
     * @return void
     */
    public function allPostsAction(): void
    {
        $perpage = (int) $this->request->getGet()->get('perpage') ?? null;
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        } elseif (!is_int($perpage) || empty($perpage)) {
            header('Location: /?page=allPosts&perpage=1');
            exit();
        }
        $post = $this->postManager->paginationPost($perpage);
        $this->view->render('backoffice', 'allPosts', ['post' => $post]);
    }
    /**
     * Display the updatePost Page
     *
     * @return void
     */
    public function updatePostAction(): void
    {
        if ($this->userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $idPost = (int) $this->request->getGet()->get('id') ?? null;
        if(empty($idPost) || $idPost == null){
            header('Location: /?page=allPosts&pp=1');
            exit();
        }
        $postBbd = $this->postManager->findPostByIdPost((int) $idPost);        
        if($idPost !== $postBbd->getIdPost()){
            header('Location: /?page=allPosts&pp=1');
            exit();
        }
        $title = $postBbd->getTitle();
        $chapo = $postBbd->getChapo();
        $description = $postBbd->getDescription();
        $this->view->render('backoffice', 'updatePost', ['id'=>$idPost, 'title'=>$title, 'chapo'=>$chapo, 'description'=>$description]);
    }
}
