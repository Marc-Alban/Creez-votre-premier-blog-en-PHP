<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\CommentManager;
use App\Service\Http\Session;
use App\View\View;

final class BackCommentController
{
    private CommentManager $commentManager;
    private View $view;
    private Session $session;
    private ?string $userSession;
    private ?string $adminSession;
    public function __construct(CommentManager $commentManager, View $view, Session $session)
    {
        $this->commentManager = $commentManager;
        $this->view = $view;
        $this->session = $session;
    }
    /**
     * display AllComments page
     *
     * @return void
     */
    public function allCommentsAction(int $perpage): void
    {
        $this->userSession =  $this->session->getSessionName('user');
        $this->adminSession =  $this->session->getSessionName('admin');
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($perpage)) {
            header('Location: /?page=allComments&perpage=1');
            exit();
        }
        $paginationComments = $this->commentManager->paginationComments($perpage);
        $this->view->render('backoffice', 'allComments', ["paginationComments" => $paginationComments]);
    }
    /**
     * method to valide Comment
     *
     * @return void
     */
    public function valideCommentAction(int $idComment, int $perpage): void
    {
        $val = $this->commentManager->valideComment($idComment);
        $paginationComments = $this->commentManager->paginationComments($perpage);
        $this->view->render('backoffice', 'allComments', ["paginationComments" => $paginationComments, 'val' => $val]);
    }
    /**
     * method to delete Comment
     *
     * @return void
     */
    public function deleteCommentAction(int $idComment, int $perpage): void
    {
        $del = $this->commentManager->deleteComment($idComment);
        $paginationComments = $this->commentManager->paginationComments($perpage);
        $this->view->render('backoffice', 'allComments', ["paginationComments" => $paginationComments, 'del' => $del]);
    }
}
