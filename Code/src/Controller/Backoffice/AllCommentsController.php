<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\AllCommentsManager;
use App\View\View;


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
        $valDel = null;

        if(!isset($userSession) && $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }

        $comments = $this->AllCommentsManager->getAllComments();
        
        if($action === 'valide' && isset($action) && $action !== null){
            $valDel = $this->AllCommentsManager->validedComment((int) $idComment);
            header('Location: /?page=allComments');
            exit();
        }if($action === 'valideSignal' && isset($action) && $action !== null){
            $valDel = $this->AllCommentsManager->validedComment((int) $idComment, 1 );
            header('Location: /?page=allComments');
            exit();
        }else if ($action === 'deleted' || $action === 'deletedSignal' && isset($action) && $action !== null){
            $valDel = $this->AllCommentsManager->deletedComment((int) $idComment);
            header('Location: /?page=allComments');
            exit();
        }

        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'valDel' => $valDel]);
    }

}