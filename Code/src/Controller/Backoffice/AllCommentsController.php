<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\AllCommentsManager;
use App\View\View;
use App\Service\Http\Session;


final class AllCommentsController
{
    private View $view;
    private AllCommentsManager $AllCommentsManager;


    public function __construct(array $classController)
    {
        // Dépendances
        $this->AllCommentsManager = $classController['manager']['managerPage'];
        $this->view = $classController['view']; 
    }

    public function AllCommentsAction(array $datas): void
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

        $comments = $this->AllCommentsManager->getAllComments();
        
        if($action === 'valide' && isset($action) && $action !== null){
            $val = $this->AllCommentsManager->validedComment((int) $idComment, 0, $datas);
            header ("Refresh: 1;/?page=allComments");
        }else if($action === 'valideSignal' && isset($action) && $action !== null){
            $val = $this->AllCommentsManager->validedComment((int) $idComment, 1, $datas );
            header ("Refresh: 1;/?page=allComments");
        }else if ($action === 'deleted' || $action === 'deletedSignal' && isset($action) && $action !== null){
            $del = $this->AllCommentsManager->deletedComment((int) $idComment, $datas);
            header ("Refresh: 1;/?page=allComments");
        }    

        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'val' => $val, 'del' => $del]);
    }

}