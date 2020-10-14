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
            header('Location: /?page=connexion');
            exit();
        }
        $comments = $this->commentManager->findAllComments();
        if ($action === 'valide' && isset($action) && $action !== null) {
            $val = $this->commentManager->validComment((int) $idComment, 0, $this->session);
            header("Refresh: 1;/?page=allComments");
        } elseif ($action === 'valideSignal' && isset($action) && $action !== null) {
            $val = $this->commentManager->validComment((int) $idComment, 1);
            header("Refresh: 1;/?page=allComments");
        } elseif ($action === 'deleted' || $action === 'deletedSignal' && isset($action) && $action !== null) {
            $del = $this->commentManager->deleteComment((int) $idComment, $this->session);
            header("Refresh: 1;/?page=allComments");
        }
        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'val' => $val, 'del' => $del]);
    }
}
