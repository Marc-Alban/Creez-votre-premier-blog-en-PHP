<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\View\View;

final class PostController
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
     * @param array|null $comments
     * @param array|null $message
     * @return void
     */
    public function postAction(?UserManager $userManager, ?array $comments, ?array $message): void
    {
        $post = null;
        $user = null;
        $defaultPost = null;
        $idPost = (int) $this->request->getGet()->get('id');
        $arrayIdBdd = $this->postManager->findAllIdPost();
        var_dump($arrayIdBdd);
        die();
        foreach ($arrayIdBdd as $k => $v) {
            if (in_array($idPost, $v, false) === true) {
                $post = $this->postManager->findPostByIdPost($idPost);
                $user = $userManager->findNameByIdUser($post->getUserId());
            } elseif (in_array($idPost, $v, false) === false) {
                $defaultPost = $this->defaultPost();
            }
        }
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, 'comments' => $comments, 'message' => $message, 'defaultPost'=> $defaultPost]);
    }
    /**
     * display the blog page
     * if don't have a post, the page take a default post in bdd
     *
     * @return void
     */
    public function postsAction(): void
    {
        $defaultPost = null;
        $perpage = (int) $this->request->getGet()->get('perpage') ?? null;
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
