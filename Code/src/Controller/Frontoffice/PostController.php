<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Entity\Post;
use App\Model\Entity\Comment;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\View\View;
use App\Controller\ErrorController;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class PostController
{
    private PostManager $postManager;
    private UserManager $userManager;
    private View $view;
    private ErrorController $error;
    private Token $token;
    private Session $session;

    public function __construct(PostManager $postManager, View $view, ErrorController $error, Token $token, Session $session)
    {
        $this->postManager = $postManager;
        $this->view = $view;
        $this->error = $error;
        $this->token = $token;
        $this->session = $session;
    }

    public function postAction(): void
    {
        $id = intval($datas['get']['id']) ?? null;
        $action = $datas['get']['action'] ?? null;
        $user = null;
        $userSession = $this->session->getSession()['user'] ?? null;
        $bugComment = null;

        $post = $this->postManager->showOne($id);
        $comments = $this->postManager->getAllComment($post->getIdPost());

        if ($action === 'signal') {
            $idComment = intval($datas['get']['idComment']) ?? null;
            $this->postManager->signalComment($idComment);
            header('Location: /?page=post&id='.$id);
            exit();
        } else if ($action === 'sendComment') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $bugComment = $this->postManager->verifComment($id, $userSession ,$datas);
        }
        
        if ($post instanceof Post) {
            $user = $this->userManager->findUser($post->getUserId());
        } else if ($post === null || empty($post)) {
            $this->error->notFound();
        }
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, "bugComment" => $bugComment, 'comment' => $comments]);
    }

    public function postsAction(): void
    {
        if (isset($data['get']['pp']) && !empty($data['get']['pp'])) {
            $paginationPost =  $this->blogManager->paginationPost($data);
        }else if(isset($data['get']['page']) && $data['get']['page'] === 'blog' && !isset($data['get']['pp'])){
            header('Location: /?page=blog&pp=1');
            exit();
        }else if(isset($data['get']['page']) || !isset($data['get']['pp']) || empty($data['get']['pp']) || $data['get']['pp'] !== '0'){
            header('Location: /?page=blog&pp=1');
            exit();
        }
        $this->view->render('Frontoffice', 'blog', ['paginationPost' => $paginationPost]);
    }


}
