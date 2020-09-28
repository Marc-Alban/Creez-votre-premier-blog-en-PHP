<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;

use App\Model\Entity\Post;
use App\Model\Entity\User;
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
    private User $userObj;

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
        $id = $datas['get']['id'] ?? null;
        $action = $datas['get']['action'] ?? null;

        $post = $this->postManager->showOne((int) $id);
        // $this->userObj = 'User';
        // $user = $this->userManager->findUser($this->userObj,'getUserName()');
        $user = null;

        if ($action === 'signalComment') {
            $this->postManager->signalComment($datas);
        } else if ($action === 'sendComment') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $this->postManager->verifComment($datas);
        }


        if ($post instanceof Post) {
            $this->view->render('Frontoffice', 'post', ["post" => $post, "user" => $user]);
        } else if ($post === null || empty($post)) {
            $this->error->ErrorAction();
        }
    }
}
