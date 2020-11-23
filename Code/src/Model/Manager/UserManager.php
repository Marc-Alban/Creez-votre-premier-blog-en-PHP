<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\AccessControl;
use App\Service\Security\Token;

final class UserManager
{
    private UserRepository $userRepository;
    private AccessControl $accessControl;
    private array $errors = [];
    private array $success = [];
    private Session $session;
    private ?string $userSession;
    private ?string $adminSession;
    public function __construct(UserRepository $userRepository, AccessControl $accessControl, Session $session)
    {
        $this->userRepository = $userRepository;
        $this->accessControl = $accessControl;
        $this->session = $session;
        $this->userSession =  $this->session->getSessionName('user');
        $this->adminSession =  $this->session->getSessionName('admin');
    }
    /**
     * Give the user with the idUser
     *
     * @param integer $idUser
     * @return User|null
     */
    public function findUserByIdUser(int $idUser): ?User
    {
        return $this->userRepository->findByIdUser($idUser);
    }
    /**
     * Find user with the session
     *
     * @return User|null
     */
    public function findUserBySession(): ?User
    {
        $user = null;
        if ($this->userSession !== null) {
            $user = $this->userRepository->findByEmail($this->userSession);
        } elseif ($this->adminSession !== null) {
            $user = $this->userRepository->findByEmail($this->adminSession);
        }
        if ($user !== null) {
            return $user;
        }
        return null;
    }
    /**
     * Find user with email
     *
     * @param string $user
     * @return User|null
     */
    public function findUserByEmail(string $user = null): ?User
    {
        return $this->userRepository->findByEmail($user);
    }
    /**
     * method to connect users or return an error
     *
     * @param Session $session
     * @param Token $token
     * @param Request $request
     * @return array
     */
    public function userLogIn(Session $session, Token $token, Request $request): array
    {
        $userBdd = $this->userRepository->findByEmail($request->getPost()->getName('email'));
        $verifUserBdd = $this->accessControl->userAction($session, $request, $userBdd);
        if (empty($request->getPost()->getName('email')) && empty($request->getPost()->getName('password'))) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif ($verifUserBdd !== null) {
            $this->errors = $verifUserBdd;
        }
        if ($token->compareTokens($session->getSessionName('token'), $request->getPost()->getName('token')) !== false) {
            $this->errors['error']['formRegister'] = "Formulaire incorrect";
        }
        if (empty($this->errors)) {
            $this->success['success'] = 'Utilisateur est bien connecté';
            return $this->success;
        }
        return $this->errors;
    }
    /**
     * Check the role of admin and change with the choice
     *
     * @param Request $request
     * @return array
     */
    public function checkUrlRole(Request $request): array
    {
        $roleUrl = $request->getGet()->getName("action");
        $idUserUrl = (int) $request->getGet()->getName("id");
        $user = $this->userRepository->findByIdUser($idUserUrl);
        $roleUser = $user->getActivated();
        if ($roleUrl === 'admin' && $roleUser === 0) {
            $this->userRepository->changeRoleUser(1, 'Admin', $idUserUrl);
            $this->success['success'] = 'Le rôle de l\'utilisateur est bien devenu : <<Admin>>';
            return $this->success;
        } elseif ($roleUrl === 'user' && $roleUser === 1) {
            $this->userRepository->changeRoleUser(0, 'Utilisateur', $idUserUrl);
            $this->success['success'] = 'Le rôle de l\'utilisateur est bien devenu : <<Utilisateur>>';
            return $this->success;
        } elseif (($roleUrl === 'user' && $roleUser === 0) || ($roleUrl === 'admin' && $roleUser === 1)) {
            $this->errors['error'] = 'Utilisateur à déjà le rôle demandé, (admin ou utilisateur) !';
        }
        return $this->errors;
    }
    /**
     * Pagination of the usermanagement page where all the user are located
     *
     * @param integer $perpage
     * @return array
     */
    public function paginationUser(int $perpage = 1): array
    {
        $minUser = 10;
        $total = $this->userRepository->count();
        $nbPage = (int) ceil($total/$minUser);
        if (ctype_digit($perpage) === true || $perpage <= 0) {
            $perpage = 1;
        } elseif ($perpage > $nbPage) {
            $perpage = $nbPage;
        }
        $page =  ($perpage-1) * $minUser;
        $user = $this->userRepository->findAll($page, $minUser);
        return [
            'current' => $perpage,
            'nbPage' => $nbPage,
            'user' => $user
        ];
    }
    /**
     * return count all user in database
     *
     * @return integer
     */
    public function countAllUser(): int
    {
        return $this->userRepository->count();
    }
    /**
     * method to save users or return an error
     *
     * @param Session $session
     * @param Token $token
     * @param Request $request
     * @return array
     */
    public function userRegister(Session $session, Token $token, Request $request): array
    {
        $dataPost = $request->getPost() ?? null;
        $pseudo = $dataPost->getName('userName') ?? null;
        $email = $dataPost->getName('email') ?? null;
        $password =  $dataPost->getName('password') ?? null;
        $passwordConfirmation = $dataPost->getName('passwordConfirmation') ?? null;
        $userName = $this->userRepository->findByPseudo($pseudo);
        $userEmail = $this->userRepository->findByEmail($email);
        if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif (empty($pseudo) || $userName !== null ||  !preg_match("#[a-zA-Z\pL\']+[\s-]?[a-zA-Z\pL\']*#", $pseudo)) {
            $this->errors['error']["pseudoEmpty"] = 'Le champs pseudo ne doit pas être vide, ni déjà pris, les caractères spéciaux ne sont pas accepté pour ce champs !';
        } elseif (empty($email) || $userEmail !== null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'Le champs email ne doit pas être vide ou être incorrect';
        } elseif ($password !== $passwordConfirmation || empty($password) || empty($passwordConfirmation)) {
            $this->errors['error']["passwordEmpty"] = 'Les champs mot de passe et mot de passe de confirmation ne doivent pas être vide et doivent correspond';
        } elseif (mb_strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
            $this->errors['error']["passwordEmpty"] = 'Le mot de passe doit avoir des minuscule-majuscule-chiffres-caractères et être supérieur à 8 caractères';
        }
        if ($token->compareTokens($session->getSessionName('token'), $dataPost->getName('token')) !== false) {
            $this->errors['error']['formRegister'] = "Formulaire incorrect";
        }
        if (empty($this->errors)) {
            $session->setSession('user', $email);
            $this->userRepository->create($email, $pseudo, $password);
            $this->success['success'] = 'Utilisateur est bien inscrit';
            return $this->success;
        }
        return $this->errors;
    }
    /**
     * method to verify password form
     *
     * @param Session $session
     * @param Request $request
     * @param Token $token
     * @param string $user
     * @return array
     */
    public function checkPassword(Session $session, Request $request, Token $token, string $user): array
    {
        $user = $this->userRepository->findByEmail($user);
        $idUser = $user->getIdUser();
        $passwordBdd = $user->getPasswordUser();
        $postForm = $request->getPost() ?? null;
        $lastPassword = $postForm->getName('lastPassword') ?? null;
        $password = $postForm->getName('password') ?? null;
        $passwordConf = $postForm->getName('passwordConfirmation') ?? null;
        if (password_verify($lastPassword, $passwordBdd) === false || empty($lastPassword)) {
            $this->errors['error']["passwordConfEmpty"] = 'Ancien mot de passe incorrect ou absent';
        } elseif (empty($password) || empty($passwordConf) || $password !== $passwordConf || mb_strlen($password) < 8 || !preg_match('#[a-zA-Z\pL\']+[\s-]?[a-zA-Z\pL\']*#', $password)) {
            $this->errors['error']["passwordEmpty"] = 'Nouveau mot de passe ou de confirmation absent, les nouveaux mot de passe doit être identique, et doit contenir 8 caractères ou plus et au format minuscule-majuscule-chiffres-caractères ';
        }
        if ($token->compareTokens($session->getSessionName('token'), $postForm->getName('token')) !== false) {
            $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
        }
        if (empty($this->errors)) {
            $this->success['success']['send'] = 'Mot de passe  bien mis à jour';
            $passhash = password_hash($password, PASSWORD_BCRYPT);
            $this->userRepository->updatePassword($passhash, $idUser);
            return $this->success;
        }
        return $this->errors;
    }
    /**
     * method to check user update form or return error
     *
     * @param Session $session
     * @param Request $request
     * @param Token $token
     * @return array
     */
    public function checkForm(Session $session, Request $request, Token $token): array
    {
        $post = $request->getPost() ?? null;
        $email = $post->getName('email') ?? null;
        $userName = $post->getName('userName') ?? null;
        $userSession =  $session->getSessionName('user') ?? $session->getSessionName('admin');
        $user = $this->userRepository->findByEmail($userSession);
        $emailBdd = null;
        $pseudoBdd = null;
        if ($user !== null) {
            $emailBdd = $user->getEmail();
            $pseudoBdd = $user->getUserName();
        }
        if (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'L\'adresse e-mail est invalide" ';
        } elseif (empty($userName) || !preg_match("#[a-zA-Z\pL\']+[\s-]?[a-zA-Z\pL\']#", $userName)) {
            $this->errors['error']["userEmpty"] = 'Veuillez mettre un nom, caractères spéciaux non accepté';
        } elseif ($userName === $pseudoBdd && $email === $emailBdd) {
            $this->errors['error']['noAction'] = 'Veuillez modifier un champs avant de soumettre !! ';
        }
        if ($token->compareTokens($session->getSessionName('token'), $post->getName('token')) !== false) {
            $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
        }
        if (empty($this->errors)) {
            $this->success['success']['send'] = 'Utilisateur bien mis à jour:';
            $this->userRepository->update($email, $userName, $user->getIdUser());
            if ($user->getActivated() === 0) {
                $session->setSession('user', $email);
            } elseif ($user->getActivated() === 1) {
                $session->setSession('admin', $email);
            }
            return $this->success;
        }
        return $this->errors;
    }
}
