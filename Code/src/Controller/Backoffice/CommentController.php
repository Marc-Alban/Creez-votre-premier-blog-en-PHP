<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\CommentManager;
use App\Service\Http\Request;
use App\View\View;


final class CommentController
{
    private CommentManager $commentManager;
    private View $view;
    private Request $request;

    public function __construct(CommentManager $commentManager, View $view, Request $request)
    {
        $this->commentManager = $commentManager;
        $this->view = $view;
        $this->request = $request;
    }

    public function commentAction(): void
    {
        $userSession = $datas['session']['user'] ?? null;
        $action = $datas['get']['action'] ?? null;
        $idComment = $datas['get']['id'] ?? null;
        $val = null;
        $del = null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        $comments = $this->commentsManager->getAllComments();
        
        if($action === 'valide' && isset($action) && $action !== null){
            $val = $this->commentsManager->validedComment((int) $idComment, 0);
            header ("Refresh: 1;/?page=allComments");
        }else if($action === 'valideSignal' && isset($action) && $action !== null){
            $val = $this->commentsManager->validedComment((int) $idComment, 1);
            header ("Refresh: 1;/?page=allComments");
        }else if ($action === 'deleted' || $action === 'deletedSignal' && isset($action) && $action !== null){
            $del = $this->commentsManager->deletedComment((int) $idComment);
            header ("Refresh: 1;/?page=allComments");
        }    

        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'val' => $val, 'del' => $del]);
    }

}