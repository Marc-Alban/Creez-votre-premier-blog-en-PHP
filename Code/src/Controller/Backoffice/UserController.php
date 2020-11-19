<?php
declare(strict_types=1);
namespace App\Controller\Backoffice;

use App\Model\Manager\CommentManager;
use App\Model\Manager\PostManager;
use App\Model\Manager\UserManager;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\View\View;

final class UserController
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
     * Display the accountManagement page
     *
     * @return void
     */
    public function accountManagementAction(): void
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
        $this->view->render('backoffice', 'accountManagement', ['user'=>$user]);
    }
    /**
     * method to modify a user
     *
     * @return void
     */
    public function updateUserAction(): void
    {
        if (($this->userSession === null && $this->adminSession === null) || ($this->userSession !== null && $this->adminSession !== null)) {
            header('Location: /?page=login');
            exit();
        }
        $verifUser = $this->userManager->checkForm($this->session, $this->request, $this->token);
        $user = $this->userManager->findUserBySession();
        $this->view->render('backoffice', 'accountManagement', ['verif' => $verifUser,'user'=> $user]);
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
    public function updatePasswordAction(): void
    {
        if (($this->userSession === null && $this->adminSession === null) || ($this->userSession !== null && $this->adminSession !== null)) {
            header('Location: /?page=login');
            exit();
        }
        $verifPassBdd = $this->userManager->checkPassword($this->session, $this->request, $this->token, $this->userSession);
        $this->view->render('backoffice', 'password', ['verif' => $verifPassBdd]);
    }
    /**
     * Display the dashboard page
     *
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
     * Display the userManagement page
     *
     * @return void
     */
    public function userManagementAction(): void
    {
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($this->request->getGet()->get('perpage'))) {
            header('Location: /?page=userManagement&perpage=1');
            exit();
        }
        $perpage = (int) $this->request->getGet()->get('perpage') ?? null;
        $paginationUser =  $this->userManager->paginationUser($perpage) ?? null;
        $this->view->render('backoffice', 'userManagement', ['paginationUser'=>$paginationUser]);
    }
    
    /**
     * Action for check role and change this
     *
     * @return void
     */
    public function userManagementRoleAction(): void
    {
        if (($this->userSession === null && $this->adminSession === null) || $this->userSession !== null) {
            header('Location: /?page=login');
            exit();
        } elseif (empty($this->request->getGet()->get('perpage'))) {
            header('Location: /?page=userManagement&perpage=1');
            exit();
        }
        $perpage = (int) $this->request->getGet()->get('perpage') ?? null;
        $paginationUser =  $this->userManager->paginationUser($perpage) ?? null;
        $roleMessage = $this->userManager->checkUrlRole($this->request);
        if (array_key_exists('success', $roleMessage)) {
            header("Refresh: 1;url=/?page=userManagement");
        }
        $this->view->render('backoffice', 'userManagement', ['paginationUser'=>$paginationUser,'roleMessage'=>$roleMessage]);
    }
}
