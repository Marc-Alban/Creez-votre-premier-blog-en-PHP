<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Manager\CommentManager;
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
    public function postAction(UserManager $userManager, ?string $nameUser, ?array $comments, ?array $message): void
    {
        $idPost = (int) $this->request->getGet()->get('id') ?? null;
        $post = $this->postManager->findByIdPost($idPost);
        $user = $userManager->findNameByIdUser($post->getUserId());
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, 'nameUser'=>$nameUser,'comment' => $comments, 'message' => $message]);
    }
    public function postsAction(): void
    {
        $perpage = (int) $this->request->getGet()->get('perpage') ?? null;
        $paginationPost =  $this->postManager->paginationPost($perpage) ?? null;
        if (empty($perpage) || $perpage === 0) {
            header('Location: /?page=posts&perpage=1');
            exit();
        } elseif ($paginationPost['post'] === null) {
            header('Location: /?page=posts&perpage=1');
            exit();
        }
        $this->view->render('Frontoffice', 'posts', ['paginationPost' => $paginationPost]);
    }
}
