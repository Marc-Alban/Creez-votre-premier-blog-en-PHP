<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\AllPostManager;
use App\Model\Manager\BlogManager;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;


final class AllPostController
{
    private View $view;
    private AllPostManager $AllPostManager;
    private BlogManager $BlogManager;

    public function __construct(array $classController)
    {
        // Dépendances
        $this->view = $classController['view'] ?? null; 
        $this->error = $classController['error'];
        $this->token = $classController['token'];
        $this->session = $classController['session'];
        $this->AllPostManager = $classController['manager']['managerPage'];
        $this->BlogManager = $classController['manager']['managerAdd'];
    }

    public function AllPostAction(array $datas): void
    {
        $userSession = $datas['session']['user'] ?? null;
        $pp = intval($datas['get']['pp']) ?? null;

        if(!isset($userSession) || $userSession === null){
            header('Location: /?page=connexion');
            exit();
        }else if(!isset($pp) || $pp === null || empty($pp) || is_string($pp)){
            header('Location: /?page=allPost&pp=1');
            exit(); 
        }

        $post = $this->BlogManager->paginationPost($datas);

        $this->view->render('backoffice', 'allPost', ['allPosts' => $post]);
    }

}