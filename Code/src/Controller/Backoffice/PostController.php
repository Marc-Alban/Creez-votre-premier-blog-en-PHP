<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;
use App\Model\Manager\PostManager;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;
use App\Service\Http\Request;
final class PostController
{
    private PostManager $postManager;
    private View $view;
    private Token $token;
    private Session $session;
    private Request $request;
    public function __construct(PostManager $postManager,View $view,Token $token,Session $session,Request $request)
    {
        $this->view = $view; 
        $this->token = $token;
        $this->session = $session;
        $this->postManager = $postManager;
        $this->request = $request;
    }
    public function addPostAction(): void
    {
        $userSession = $this->session['user'] ?? null;
        $action = $this->request->getGet()->get('action') ?? null;
        $valdel = null;
        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }
        if(isset($action) && $action === 'addPost'){
            $this->session->setParamSession('token', $this->token->createSessionToken());
            $valdel = $this->postManager->verifFormAddPost($this->session,$this->token,$this->request);
        }
        $this->view->render('backoffice', 'addPost', ["valdel" => $valdel]);
    }
    public function allPostAction(): void
    {
        $userSession = $this->session['user'] ?? null;
        $pp = intval($this->request->getGet()->get('pp')) ?? null;
        if(!isset($userSession) || $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }else if(!isset($pp) || $pp === null || empty($pp) || is_string($pp)){
            header('Location: /?page=allPost&pp=1');
            exit(); 
        }
        $post = $this->postManager->paginationPost($pp);
        $this->view->render('backoffice', 'allPost', ['allPosts' => $post]);
    }
}