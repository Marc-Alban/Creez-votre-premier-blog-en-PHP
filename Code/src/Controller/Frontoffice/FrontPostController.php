<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\View\View;

final class FrontPostController
{
    private PostManager $postManager;
    private View $view;
    private Request $request;
    public function __construct(PostManager $postManager, View $view, Request $request)
    {
        $this->postManager = $postManager;
        $this->view = $view;
        $this->request = $request;
    }
    /**
     * diplay the post page
     *
     * @param UserManager|null $userManager
     * @param CommentManager $comments
     * @return void
     */
    public function postAction(?UserManager $userManager,CommentManager $comment): void
    {
        $post = null;
        $user = null;
        $defaultPost = null;
        $idPost = (int) $this->request->getGet()->getName('id');
        $post = $this->postManager->findPostByIdPost($idPost);
        if ($post !== null) {
            $user = $userManager->findUserByIdUser($post->getUserId());
        } elseif ($post === null) {
            $defaultPost = $this->defaultPost();
        }
        $comments = $comment->findCommentByPostId($idPost);
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, 'comments' => $comments,'defaultPost'=> $defaultPost]);
    }
    /**
     * display the blog page
     * if don't have a post, the page take a default post in bdd
     *
     * @return void
     */
    public function postsAction(int $perpage): void
    {
        $defaultPost = null;
        $paginationPost =  $this->postManager->paginationPost($perpage) ?? null;
        if ($paginationPost['post'] === null) {
            $defaultPost = $this->defaultPost();
        }
        $this->view->render('Frontoffice', 'posts', ['paginationPost' => $paginationPost, 'defaultPost'=> $defaultPost]);
    }
    /**
     * Display default post
     *
     * @return array
     */
    public function defaultPost(): array
    {
        return $this->postManager->defaultPost();
    }
}
