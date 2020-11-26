<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\PostManager;
use App\Model\Repository\UserRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class BackPostController
{
    private PostManager $postManager;
    private View $view;
    private Token $token;
    private Session $session;
    private Request $request;
    private ?string $userSession;
    private ?string $adminSession;
    public function __construct(PostManager $postManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->postManager = $postManager;
        $this->request = $request;
        $this->userSession =  $this->session->getSessionName('user');
        $this->adminSession =  $this->session->getSessionName('admin');
    }
    /**
     * display addPost Page
     *
     * @return void
     */
    public function addPostBackAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'addPostback', []);
    }
    /**
     *  method to add a post in bdd
     *
     * @param UserRepository $userRepository
     * @return void
     */
    public function addPostBddBackAction(UserRepository $userRepository): void
    {
        $valdel = null;
        $title = null;
        $chapo = null;
        $description = null;
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        }
        $valdel = $this->postManager->checkFormPost($userRepository, $this->session, $this->token, $this->request, 'create');
        if ($valdel !== ['success']) {
            $post = $this->request->getPost();
            $title = $post->getName('title');
            $chapo = $post->getName('chapo');
            $description = $post->getName('description');
        }
        $this->view->render('backoffice', 'addPostback', ["valdel" => $valdel, "title"=>$title,"chapo"=>$chapo,"description"=>$description]);
    }
    /**
     * Display allPostPage
     *
     * @param integer $perpage
     * @return void
     */
    public function allPostsBackAction(int $perpage): void
    {
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (!is_int($perpage) || empty($perpage)) {
            header('Location: /?page=allPostsBack&perpage=1');
            exit();
        }
        $post = $this->postManager->paginationPost($perpage);
        $this->view->render('backoffice', 'allPostsback', ['post' => $post]);
    }
    /**
     * Display the updatePost Page
     *
     * @param integer $idPost
     * @return void
     */
    public function updatePostBackAction(int $idPost): void
    {
        $postBbd = $this->postManager->findPostByIdPost($idPost);
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($idPost) || $postBbd === null || $idPost !== $postBbd->getIdPost()) {
            header('Location: /?page=allPostsBack&perpage=1');
            exit();
        }
        $title = $postBbd->getTitle();
        $chapo = $postBbd->getChapo();
        $description = $postBbd->getDescription();
        $this->view->render('backoffice', 'updatePostBack', ['id'=>$idPost, 'title'=>$title, 'chapo'=>$chapo, 'description'=>$description]);
    }

    /**
     * Action of the updatePost Page
     *
     * @param UserRepository $userRepository
     * @param integer $idPost
     * @return void
     */
    public function updatePostBddBackAction(UserRepository $userRepository, int $idPost): void
    {
        $postBbd = $this->postManager->findPostByIdPost($idPost);
        $post = null;
        $title = null;
        $chapo = null;
        $description = null;
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($idPost) || $postBbd === null || $idPost !== $postBbd->getIdPost()) {
            header('Location: /?page=allPostsBack&perpage=1');
            exit();
        }
        $valdel = $this->postManager->checkFormPost($userRepository, $this->session, $this->token, $this->request, 'update');
        if ($valdel !== ['success']) {
            $post = $this->request->getPost();
            $title = $post->getName('title');
            $chapo = $post->getName('chapo');
            $description = $post->getName('description');
        } elseif ($valdel === ['success']) {
            $title = $postBbd->getTitle();
            $chapo = $postBbd->getChapo();
            $description = $postBbd->getDescription();
        }
        $this->view->render('backoffice', 'updatePostBack', ["valdel" => $valdel, 'id'=>$idPost, 'title'=>$title, 'chapo'=>$chapo, 'description'=>$description]);
    }
    /**
     *  Function for delete a post in database
     *
     * @param integer $idPost
     * @param integer $perpage
     * @return void
     */
    public function deletePostBackAction(int $idPost, int $perpage): void
    {
        $postBbd = $this->postManager->findPostByIdPost($idPost);
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (!is_int($perpage) || empty($perpage) || !is_int($perpage) || empty($perpage) || empty($idPost) || $postBbd === null || $idPost !== $postBbd->getIdPost()) {
            header('Location: /?page=allPostsBack&perpage=1');
            exit();
        }
        $delPost = $this->postManager->deletePost($idPost);
        $post = $this->postManager->paginationPost($perpage);
        $this->view->render('backoffice', 'allPostsBack', ['post' => $post, "delPost"=>$delPost]);
    }
}
