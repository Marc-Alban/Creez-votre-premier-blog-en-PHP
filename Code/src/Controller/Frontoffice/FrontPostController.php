<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Parameter;
use App\Service\Http\Session;
use App\View\View;

final class FrontPostController
{
    private PostManager $postManager;
    private View $view;
    public function __construct(PostManager $postManager, View $view)
    {
        $this->postManager = $postManager;
        $this->view = $view;
    }
    /**
     *diplay the post page
    *
    * @param UserManager|null $userManager
    * @param CommentManager $comment
    * @param integer $idPost
    * @return void
    */
    public function postAction(?UserManager $userManager, CommentManager $comment, Session $session, Parameter $Get, int $idPost): void
    {
        $post = null;
        $user = null;
        $defaultPost = null;
        $post = $this->postManager->findPostByIdPost($idPost);
        if ($session->getSessionName('validation') !== null && $Get->getName('validation') === 'valide') {
            header("Refresh: 1;url=/?page=post&id=".$idPost."&#message");
            $session->sessionDestroyName("validation");
        }
        if ($post !== null) {
            $user = $userManager->findUserByIdUser($post->getUserId());
        } elseif ($post === null) {
            $defaultPost = $this->defaultPost();
        }
        $comments = $comment->findCommentByPostId($idPost);
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, 'comments' => $comments,'defaultPost'=> $defaultPost]);
    }
    /**
     * Display the blog page
     * if don't have a post, the page take a default post in bdd
     *
     * @param integer $perpage
     * @return void
     */
    public function blogAction(int $perpage): void
    {
        $defaultPost = null;
        $paginationPost =  $this->postManager->paginationPost($perpage) ?? null;
        if ($paginationPost['post'] === null) {
            $defaultPost = $this->defaultPost();
        }
        $this->view->render('Frontoffice', 'blog', ['paginationPost' => $paginationPost, 'defaultPost'=> $defaultPost]);
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
