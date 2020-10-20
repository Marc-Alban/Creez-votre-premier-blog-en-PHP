<?php

declare(strict_types=1);

namespace App\Controller\Backoffice;

use App\Model\Manager\CommentManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\View\View;

final class CommentController
{
    private CommentManager $commentManager;
    private View $view;
    private Session $session;
    private Request $request;
    public function __construct(CommentManager $commentManager, View $view, Session $session, Request $request)
    {
        $this->commentManager = $commentManager;
        $this->view = $view;
        $this->session = $session;
        $this->request = $request;
    }
    public function commentAction(): void
    {
        $userSession = $this->session->getSessionName('user') ?? null;
        $action = $this->request->getGet()->get('action') ?? null;
        $idComment = $this->request->getGet()->get('id') ?? null;
        $val = null;
        $del = null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /connexion');
            exit();
        }
        $comments = $this->commentManager->findAllComments();
        if ($action === 'valide') {
            $val = $this->commentManager->validComment((int) $idComment, 0);
            header("Refresh: 1;/allComments");
        } elseif ($action === 'valideSignal') {
            $val = $this->commentManager->validComment((int) $idComment, 1);
            header("Refresh: 1;/allComments");
        } elseif ($action === 'deleted' || $action === 'deletedSignal') {
            $del = $this->commentManager->deleteComment((int) $idComment);
            header("Refresh: 1;/allComments");
        }
        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'val' => $val, 'del' => $del]);
    }
}
