<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;
use App\Service\Security\AccessControl;


final class UserManager
{
    private UserRepository $userRepository;
    private AccessControl $accessControl;
    private $errors = null;
    private $success = null;
    public function __construct(UserRepository $userRepository, AccessControl $accessControl)
    {
        $this->userRepository = $userRepository;
        $this->accessControl = $accessControl;
    }
    /**
     * Give the name with the idUser
     *
     * @param integer $idUser
     * @return User|null
     */
    public function findNameByIdUser(int $idUser): ?User
    {
        return $this->userRepository->findByIdUser($idUser);
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
        $userBdd = $this->userRepository->findByEmail($request->getPost()->get('email'));
        $verifUserBdd = $this->accessControl->userAction($session,$request,$userBdd);
        if (empty($request->getPost()->get('email')) && empty($request->getPost()->get('password'))) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif (array_key_exists('error',$verifUserBdd) || $verifUserBdd !== null){
            $this->errors = $verifUserBdd;
        }
        if ($token->compareTokens($session->getSessionName('token'), $request->getPost()->get('token')) !== false) {
            $this->errors['error']['formRegister'] = "Formulaire incorrect";
        }
        if (empty($this->errors)) {
        $this->success['success'] = 'Utilisateur est bien connecté';
        return $this->success;
        }
        return $this->errors;
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
        $pseudo = $dataPost->get('userName') ?? null;
        $email = $dataPost->get('email') ?? null;
        $password =  $dataPost->get('password') ?? null;
        $passwordConfirmation = $dataPost->get('passwordConfirmation') ?? null;
        $userName = $this->userRepository->findByPseudo($pseudo);
        $userEmail = $this->userRepository->findByEmail($email);
        if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif (empty($pseudo) || $userName !== null || preg_match('/[][(){}<>\/+"*%&=?#`^\'!$_:;,-]/', $pseudo)) {
            $this->errors['error']["pseudoEmpty"] = 'Le champs pseudo ne doit pas être vide, ni déjà pris, les caractères spéciaux ne sont pas accepté pour ce champs !';
        } elseif (empty($email) || $userEmail !== null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'Le champs email ne doit pas être vide ou être incorrect';
        } elseif ($password !== $passwordConfirmation || empty($password) || empty($passwordConfirmation)) {
            $this->errors['error']["passwordEmpty"] = 'Les champs mot de passe et mot de passe de confirmation ne doivent pas être vide et doivent correspond';
        } elseif (mb_strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
            $this->errors['error']["passwordEmpty"] = 'Le mot de passe doit avoir des minuscule-majuscule-chiffres-caractères et être supérieur à 8 caractères';
        }
        if ($token->compareTokens($session->getSessionName('token'), $dataPost->get('token')) !== false) {
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
        $lastPassword = $postForm->get('lastPassword') ?? null;
        $password = $postForm->get('password') ?? null;
        $passwordConf = $postForm->get('passwordConfirmation') ?? null;
        if (password_verify($lastPassword, $passwordBdd) === false || empty($lastPassword)) {
            $this->errors['error']["passwordConfEmpty"] = 'Ancien mot de passe incorrect ou absent';
        } elseif (empty($password) || empty($passwordConf) || $password !== $passwordConf || mb_strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
            $this->errors['error']["passwordEmpty"] = 'Nouveau mot de passe ou de confirmation absent, les nouveaux mot de passe doit être identique, et doit contenir 8 caractères ou plus et au format minuscule-majuscule-chiffres-caractères ';
        }
        if ($token->compareTokens($session->getSessionName('token'), $postForm->get('token')) !== false) {
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
        $email = $post->get('email') ?? null;
        $userName = $post->get('userName') ?? null;
        $userSession =  $session->getSessionName('user');
        $user = $this->userRepository->findByEmail($userSession);
        $emailBdd = null;
        $pseudoBdd = null;
        if ($user !== null) {
            $emailBdd = $user->getEmail();
            $pseudoBdd = $user->getUserName();
        }
        if (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'L\'adresse e-mail est invalide" ';
        } elseif (empty($userName) || preg_match('/[][(){}<>\/+"*%&=?#`^\'!$_:;,-]/', $userName)) {
            $this->errors['error']["userEmpty"] = 'Veuillez mettre un nom, caractères spéciaux non accepté';
        } elseif ($userName === $pseudoBdd && $email === $emailBdd) {
            $this->errors['error']['noAction'] = 'Veuillez modifier un champs avant de soumettre !! ';
        }
        if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
            $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
        }

        if (empty($this->errors)) {
            $this->success['success']['send'] = 'Utilisateur bien mis à jour:';
            $this->userRepository->update($email, $userName, $user->getIdUser());
            ($session->getSession('user'))? $session->setSession('user', $email) : $session->setSession('userAdmin', $email);
            return $this->success;
        }
        return $this->errors;
    }
}
