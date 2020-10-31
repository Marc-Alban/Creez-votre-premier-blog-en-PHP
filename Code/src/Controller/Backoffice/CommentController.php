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
    public function allCommentsAction(): void
    {
        $userSession = $this->session->getSessionName('user') ?? null;
        if (!isset($userSession) && is_null($userSession)) {
            header('Location: /?page=login');
        }
        $comments = $this->commentManager->findAllComments();
        $this->view->render('backoffice', 'allComments', ["comments" => $comments]);
    }
    public function validCommentAction(): void
    {
        $val = null;
        $idComment = $this->request->getGet()->get('id') ?? null;
        $comments = $this->commentManager->findAllComments();
        $val = $this->commentManager->validComment((int) $idComment, 0);
        header("Location: /?page=allComments");
        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'val' => $val]);
    }
    public function deleteCommentAction(): void
    {
        $del = null;
        $idComment = $this->request->getGet()->get('id') ?? null;
        $comments = $this->commentManager->findAllComments();
        $del = $this->commentManager->deleteComment((int) $idComment);
        header("Location: /?page=allComments");
        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'del' => $del]);
    }
}
