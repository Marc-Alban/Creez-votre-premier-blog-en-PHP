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
    private int $idComment;
    public function __construct(CommentManager $commentManager, View $view, Session $session, Request $request)
    {
        $this->commentManager = $commentManager;
        $this->view = $view;
        $this->session = $session;
        $this->request = $request;
        $this->idComment = (int) $this->request->getGet()->get('id') ?? null;
    }
    public function allCommentsAction(): void
    {
        $userSession = $this->session->getSessionName('user') ?? null;
        if (!isset($userSession) && $userSession === null) {
            header('Location: /?page=login');
            exit();
        }
        $comments = $this->commentManager->findAllComments();
        $this->view->render('backoffice', 'allComments', ["comments" => $comments]);
    }
    public function valideCommentAction(): void
    {
        $val = $this->commentManager->valideComment($this->idComment);
        $comments = $this->commentManager->findAllComments();
        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'val' => $val]);
    }
    public function deleteCommentAction(): void
    {
        $del = $this->commentManager->deleteComment($this->idComment);
        $comments = $this->commentManager->findAllComments();
        $this->view->render('backoffice', 'allComments', ["comments" => $comments, 'del' => $del]);
    }
}
