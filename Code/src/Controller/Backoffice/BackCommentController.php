<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\CommentManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\View\View;

final class BackCommentController
{
    private CommentManager $commentManager;
    private View $view;
    private Session $session;
    private Request $request;
    private int $idComment;
    private ?string $userSession;
    private ?string $adminSession;
    public function __construct(CommentManager $commentManager, View $view, Session $session, Request $request)
    {
        $this->commentManager = $commentManager;
        $this->view = $view;
        $this->session = $session;
        $this->request = $request;
        $this->idComment = (int) $this->request->getGet()->getName('id') ?? null;
    }
    /**
     * display AllComments page
     *
     * @return void
     */
    public function allCommentsAction(): void
    {
        $this->userSession =  $this->session->getSessionName('user');
        $this->adminSession =  $this->session->getSessionName('admin');
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($this->request->getGet()->getName('perpage'))) {
            header('Location: /?page=allComments&perpage=1');
            exit();
        }
        $perpage = (int) $this->request->getGet()->getName('perpage') ?? null;
        $paginationComments = $this->commentManager->paginationComments($perpage);
        $this->view->render('backoffice', 'allComments', ["paginationComments" => $paginationComments]);
    }
    /**
     * method to valide Comment
     *
     * @return void
     */
    public function valideCommentAction(): void
    {
        $val = $this->commentManager->valideComment($this->idComment);
        $perpage = (int) $this->request->getGet()->getName('perpage') ?? null;
        $paginationComments = $this->commentManager->paginationComments($perpage);
        $this->view->render('backoffice', 'allComments', ["paginationComments" => $paginationComments, 'val' => $val]);
    }
    /**
     * method to delete Comment
     *
     * @return void
     */
    public function deleteCommentAction(): void
    {
        $del = $this->commentManager->deleteComment($this->idComment);
        $perpage = (int) $this->request->getGet()->getName('perpage') ?? null;
        $paginationComments = $this->commentManager->paginationComments($perpage);
        $this->view->render('backoffice', 'allComments', ["paginationComments" => $paginationComments, 'del' => $del]);
    }
}
