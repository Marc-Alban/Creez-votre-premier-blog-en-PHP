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
    public function addPostAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
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
    public function addPostBddAction(UserRepository $userRepository): void
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
        $this->view->render('backoffice', 'addPost', ["valdel" => $valdel, "title"=>$title,"chapo"=>$chapo,"description"=>$description]);
    }
    /**
     * Display allPostPage
     *
     * @return void
     */
    public function allPostsAction(int $perpage): void
    {
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
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
    public function updatePostAction(int $idPost): void
    {
        $postBbd = $this->postManager->findPostByIdPost($idPost);
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($idPost) || $postBbd === null || $idPost !== $postBbd->getIdPost()) {
            header('Location: /?page=allPosts&perpage=1');
            exit();
        }
        $title = $postBbd->getTitle();
        $chapo = $postBbd->getChapo();
        $description = $postBbd->getDescription();
        $this->view->render('backoffice', 'updatePost', ['id'=>$idPost, 'title'=>$title, 'chapo'=>$chapo, 'description'=>$description]);
    }

    /**
     * Action of the updatePost Page
     *
     * @return void
     */
    public function updatePostBddAction(UserRepository $userRepository, int $idPost): void
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
            header('Location: /?page=allPosts&perpage=1');
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
        $this->view->render('backoffice', 'updatePost', ["valdel" => $valdel, 'id'=>$idPost, 'title'=>$title, 'chapo'=>$chapo, 'description'=>$description]);
    }
    /**
     * Function for delete a post in database
     *
     * @return void
     */
    public function deletePostAction(int $idPost, int $perpage): void
    {
        $postBbd = $this->postManager->findPostByIdPost($idPost);
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (!is_int($perpage) || empty($perpage) || !is_int($perpage) || empty($perpage) || empty($idPost) || $postBbd === null || $idPost !== $postBbd->getIdPost()) {
            header('Location: /?page=allPosts&perpage=1');
            exit();
        }
        $delPost = $this->postManager->deletePost($idPost);
        $post = $this->postManager->paginationPost($perpage);
        $this->view->render('backoffice', 'allPosts', ['post' => $post, "delPost"=>$delPost]);
    }
}
