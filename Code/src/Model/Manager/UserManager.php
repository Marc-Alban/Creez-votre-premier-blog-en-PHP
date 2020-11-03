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
    public function findNameByIdUser(int $idUser): ?User
    {
        return $this->userRepository->findById($idUser);
    }
    public function findByUserEmail(string $user = null): ?User
    {
        return $this->userRepository->findByEmail($user);
    }
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
            $this->errors['error']['formRgister'] = "Formulaire incorrect";
        }
        if (empty($this->errors)) {
            $session->setSession('user', $email);
            $this->success['success'] = 'Utilisateur est bien connecté';
            return $this->success;
        }
        return $this->errors;
    }
    // public function userRegister(Session $session, Token $token, Request $request): array
    // {
    //     $dataPost = $request->getPost() ?? null;
    //     $pseudo = $dataPost->get('userName') ?? null;
    //     $email = $dataPost->get('email') ?? null;
    //     $password =  $dataPost->get('password') ?? null;
    //     // $this->findByUserEmail($email);
    //     // $pseudoBdd = $this->userRepository->findByName($pseudo);
    //     // $emailBdd =
    //     $passwordConfirmation = $dataPost->get('passwordConfirmation') ?? null;
    //     if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
    //         $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
    //     } elseif ($pseudo === null || $pseudoBdd !== null) {
    //         $this->errors['error']["pseudoEmpty"] = 'Pseudo manquant ou déjà pris';
    //     } elseif ($email === null || $emailBdd !== null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
    //         $this->errors['error']["emailEmpty"] = 'Champs email vide ou l\'email est déjà prise';
    //     } elseif ($password !== $passwordConfirmation || empty($password)) {
    //         $this->errors['error']["passwordEmpty"] = 'Les champs mot de passe et mot de passe de confirmation sont vide ou ne correspond pas';
    //     } elseif (mb_strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
    //         $this->errors['error']["passwordEmpty"] = 'Le mot de passe doit avoir des minuscule-majuscule-chiffres-caractères et être supérieur à 8 caractères';
    //     }
    //     if ($token->compareTokens($session->getSessionName('token'), $dataPost->get('token')) !== false) {
    //         $this->errors['error']['formRgister'] = "Formulaire incorrect";
    //     }
    //     if (empty($this->errors)) {
    //         $session->setSession('user', $email);
    //         $this->userRepository->create($email, $pseudo, $password);
    //         $this->success['success'] = 'Utilisateur est bien inscrit';
    //         return $this->success;
    //     }
    //     return $this->errors;
    // }
    public function checkPassword(Session $session, Request $request, Token $token, string $user)
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
    
    public function checkForm(Session $session, Request $request, Token $token)
    {
        $post = $request->getPost() ?? null;
        $email = $post->get('email') ?? null;
        $userName = $post->get('userName') ?? null;
        $userSession =  $session->getSessionName('user');
        $user = $this->findByUserEmail($userSession);
        if (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'L\'adresse e-mail est invalide" ';
        } elseif (empty($userName)) {
            $this->errors['error']["userEmpty"] = 'Veuillez mettre un utilisateur';
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
