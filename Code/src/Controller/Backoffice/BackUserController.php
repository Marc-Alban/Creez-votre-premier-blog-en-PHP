<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\AccessControl;
use App\Service\Security\Token;
use App\View\View;

final class BackUserController
{
    private UserManager $userManager;
    private View $view;
    private Token $token;
    private Session $session;
    private Request $request;
    private ?string $userSession;
    private ?string $adminSession;
    public function __construct(UserManager $userManager, View $view, Token $token, Session $session, Request $request)
    {
        $this->userManager = $userManager;
        $this->view = $view;
        $this->token = $token;
        $this->session = $session;
        $this->request = $request;
        $this->userSession =  $this->session->getSessionName('user');
        $this->adminSession =  $this->session->getSessionName('admin');
    }
    /**
     * Display the managementAccount page
     *
     * @return void
     */
    public function managementAccountAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if (($this->userSession === null && $this->adminSession === null) || ($this->userSession !== null && $this->adminSession !== null)) {
            header('Location: /?page=login');
            exit();
        }
        $user = $this->userManager->findUserBySession();
        if ($user === null) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'managementAccount', ['user'=>$user]);
    }
    /**
     * method to modify a user
     *
     * @return void
     */
    public function managementUpdateAccountAction(): void
    {
        if (($this->userSession === null && $this->adminSession === null) || ($this->userSession !== null && $this->adminSession !== null)) {
            header('Location: /?page=login');
            exit();
        }
        $user = $this->userManager->findUserBySession();
        if ($user === null) {
            header('Location: /?page=managementAccount');
            exit();
        }
        $this->userManager->checkForm($this->session, $this->request, $this->token);
        $error = $this->session->getMessage('error');
        $success = $this->session->getMessage('success');
        $this->view->render('backoffice', 'managementAccount', ['user'=> $user,'error'=> $error,'success'=> $success]);
    }
    /**
     * Display the password page
     *
     * @return void
     */
    public function passwordAction(): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if (($this->userSession === null && $this->adminSession === null) || ($this->userSession !== null && $this->adminSession !== null)) {
            header('Location: /?page=login');
            exit();
        }
        $this->view->render('backoffice', 'password', []);
    }
    /**
     * Methode to modify the password user
     *
     * @return void
     */
    public function passwordUpdateAction(): void
    {
        if (($this->userSession === null && $this->adminSession === null) || ($this->userSession !== null && $this->adminSession !== null)) {
            header('Location: /?page=login');
            exit();
        }
        $userSession = $this->userSession ?? $this->adminSession ;
        $verifPassBdd = $this->userManager->checkPassword($this->session, $this->request, $this->token, $userSession);
        $this->view->render('backoffice', 'password', ['verif' => $verifPassBdd]);
    }
    /**
     * Display the dashboard page
     *
     * @param CommentManager $commentManager
     * @param PostManager $postManager
     * @return void
     */
    public function dashboardAction(CommentManager $commentManager, PostManager $postManager): void
    {
        $this->session->setSession('token', $this->token->createSessionToken());
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        }
        $commentDisable = $commentManager->countAllCommentsDisabled();
        $comment = $commentManager->countAllComments();
        $post = $postManager->countAllPost();
        $user = $this->userManager->countAllUser();
        $this->view->render('backoffice', 'dashboard', ["commentDisable"=>$commentDisable,"comment"=>$comment,"post"=>$post,'user'=>$user]);
    }
    /**
     *  Display the userManagement page
     *
     * @param integer $perpage
     * @return void
     */
    public function userManagementAction(int $perpage): void
    {
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($this->request->getGet()->getName('perpage'))) {
            header('Location: /?page=userManagement&perpage=1');
            exit();
        }
        $paginationUser =  $this->userManager->paginationUser($perpage) ?? null;
        $this->view->render('backoffice', 'userManagement', ['paginationUser'=>$paginationUser]);
    }
    
    /**
     *  Action for check role and change this
     *
     * @param AccessControl $accessControl
     * @param integer $perpage
     * @return void
     */
    public function userManagementRoleAction(AccessControl $accessControl, int $idUserUrl, int $perpage, string $action): void
    {
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($this->request->getGet()->getName('perpage'))) {
            header('Location: /?page=userManagement&perpage=1');
            exit();
        }
        $paginationUser =  $this->userManager->paginationUser($perpage);
        $this->userManager->checkUrlRole($idUserUrl, $action);
        $admin = $this->userManager->findUserByIdUser($idUserUrl)->getEmail();
        if ($this->adminSession === $admin) {
            $accessControl->IsAdmin($this->session);
        }
        $error = $this->session->getMessage('error');
        $success = $this->session->getMessage('success');
        $this->view->render('backoffice', 'userManagement', ['paginationUser'=>$paginationUser,'error'=> $error,'success'=> $success]);
    }
}
