<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class UserManager
{
    private UserRepository $userRepository;
    private $errors = null;
    private $success = null;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
    public function findByUserEmail(string $user = null): ?User
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
        $dataPost = $request->getPost();
        $email = $dataPost->get('email') ?? null;
        $password = $dataPost->get('password') ?? null;
        $emailBdd = null;
        $passwordBdd = null;
        $user = $this->findByUserEmail($email);
        if ($user !== null) {
            $emailBdd = $user->getEmail();
            $passwordBdd = $user->getPasswordUser();
        }
        if (empty($email) && empty($password)) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif ($email === null || $emailBdd === null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'Identifiants incorrect';
        } elseif (empty($password) || !password_verify($password, $passwordBdd)) {
            $this->errors['error']["passwordEmpty"] = 'Identifiants incorrect';
        }
        if ($token->compareTokens($session->getSessionName('token'), $dataPost->get('token')) !== false) {
            $this->errors['error']['formRegister'] = "Formulaire incorrect";
        }
        if (empty($this->errors)) {
            $session->setSession('user', $email);
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
        $userEmail = $this->findByUserEmail($email);
        if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif (empty($pseudo) || $userName !== null) {
            $this->errors['error']["pseudoEmpty"] = 'Le champs pseudo est vide ou est déjà utilisé';
        } elseif (empty($email) || $userEmail !== null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'Le champs email est vide ou est incorrect';
        } elseif ($password !== $passwordConfirmation || empty($password) || empty($passwordConfirmation)) {
            $this->errors['error']["passwordEmpty"] = 'Les champs mot de passe et mot de passe de confirmation sont vide ou ne correspond pas';
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
        $user = $this->findByUserEmail($user);
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
        $emailBdd = null;
        $pseudoBdd = null;
        $user = $this->findByUserEmail($userSession);
        if ($user !== null) {
            $emailBdd = $user->getEmail();
            $pseudoBdd = $user->getUserName();
        }

        if (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'L\'adresse e-mail est invalide" ';
        } elseif (empty($userName)) {
            $this->errors['error']["userEmpty"] = 'Veuillez mettre un utilisateur';
        } elseif ($userName === $pseudoBdd && $email === $emailBdd) {
            $this->errors['error']['noAction'] = 'Veuillez modifier un champs avant de soumettre !! ';
        } elseif (($pseudoBdd !== $userName && $email === $emailBdd) || ($email !== $emailBdd && $pseudoBdd === $userName)) {
            $this->errors['error']['alreadyTaken'] = 'Email ou pseudo déjà pris';
        }
        if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
            $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
        }

        if (empty($this->errors)) {
            $this->success['success']['send'] = 'Utilisateur bien mis à jour:';
            $this->userRepository->update($email, $userName, $user->getIdUser());
            $session->setSession('user', $email);
            return $this->success;
        }
        return $this->errors;
    }
}
// elseif($email === $emailBdd){
//     $this->errors['error']['alreadyTakenMail'] = 'le mail est déjà pris !! ';
// }
