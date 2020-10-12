<?php
declare(strict_types=1);
namespace App\Controller\Frontoffice;
use App\Model\Entity\Post;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Model\Manager\CommentManager;
use App\View\View;
use App\Controller\ErrorController;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\Service\Http\Request;
final class PostController
{
    private PostManager $postManager;
    private UserManager $userManager;
    private CommentManager $commentManager;
    private View $view;
    private ErrorController $error;
    private Token $token;
    private Session $session;
    private Request $request;
    public function __construct(PostManager $postManager,View $view,Request $request,ErrorController $error,Token $token,Session $session)
    {
        $this->postManager = $postManager;
        $this->view = $view;
        $this->error = $error;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
    }
    public function postAction(): void
    {
        $id = intval($this->request->getGet()->get('id')) ?? null;
        $action = $$this->request->getGet()->get('action') ?? null;
        $userSession = $this->session->getSession()['user'] ?? null;
        $post = $this->postManager->showOne($id);
        $comments = $this->commentManager->getAllComment($post->getIdPost());
        $user = null;
        $bugComment = null;
        if ($action === 'signal') {
            $idComment = intval($this->request->getGet()->get('idComment')) ?? null;
            $this->commentManager->signalComment($idComment);
            header('Location: /?page=post&id='.$id);
            exit();
        } else if ($action === 'sendComment') {
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $bugComment = $this->commentManager->verifComment($id,$userSession,$this->request,$this->session,$this->token);
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
        $pp = intval($this->request->getGet()->get('pp')) ?? null;
        $page = $this->request->getGet()->get('page') ?? null;
        if (isset($pp) && !empty($pp)) {
            $paginationPost =  $this->blogManager->paginationPost($pp);
        }else if(isset($page) && $page === 'blog' && !isset($pp)){
            header('Location: /?page=blog&pp=1');
            exit();
        }else if(isset($page) || !isset($pp) || empty($pp) || $pp !== '0'){
            header('Location: /?page=blog&pp=1');
            exit();
        }
        $this->view->render('Frontoffice', 'blog', ['paginationPost' => $paginationPost]);
    }
}
