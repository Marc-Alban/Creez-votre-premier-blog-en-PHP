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

    public function __construct(array $classController)
    {
        // Dépendances
        $this->view = $classController['view'];
        $this->postManager = $classController['manager']['managerPage'];
        $this->userManager = $classController['manager']['managerAdd'];
        $this->error = $classController['error'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
    }

    public function PostAction(array $datas): void
    {
        $id = intval($datas['get']['id']) ?? null;
        $action = $datas['get']['action'] ?? null;
        $user = null;
        $userSession = $this->session->getSession()['user'] ?? null;
        $bugComment = null;

        $post = $this->postManager->showOne($id);
        $comments = $this->postManager->getAllComment($post->getIdPost());
        
        if ($action === 'signal') {
            $this->postManager->signalComment((int) $comments[0]['idComment']);
            header('Location: /?page=post&id='.$id);
            exit();
        } else if ($action === 'sendComment') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $bugComment = $this->postManager->verifComment($id, $userSession ,$datas);
        }
        
        if ($post instanceof Post) {
            $user = $this->userManager->findUser($post->getUserId());
        } else if ($post === null || empty($post)) {
            $this->error->ErrorAction();
        }
        $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user, "bugComment" => $bugComment, 'comment' => $comments]);
    }
}
