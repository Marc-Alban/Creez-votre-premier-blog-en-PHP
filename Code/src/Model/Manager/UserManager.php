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
    public function findByUserEmail(string $user): ?User
    {
        return $this->userRepository->findByEmail($user);
    }
    public function userLogIn(Session $session, Token $token, Request $request): array
    {
        $dataPost = $request->getPost() ?? null;
        $email = $dataPost->get('email') ?? null;
        $emailObject = $this->userRepository->findByEmail($email);
        $emailBdd = $emailObject->getEmail();
        $password = $dataPost->get('password') ?? null;
        $passwordBdd = $this->userRepository->findPasswordByUserOrEmail(null, $email);
        if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif ($email === null || $emailBdd === null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'Identifiants incorrect ';
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
    public function userRegister(Session $session, Token $token, Request $request): array
    {
        $dataPost = $request->getPost() ?? null;
        $pseudo = $dataPost->get('userName') ?? null;
        $pseudoBdd = $this->userRepository->findByName($pseudo);
        $email = $dataPost->get('email') ?? null;
        $emailBdd = $this->userRepository->findByEmail($email);
        $password =  $dataPost->get('password') ?? null;
        $passwordConfirmation = $dataPost->get('passwordConfirmation') ?? null;
        if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
            $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
        } elseif ($pseudo === null || $pseudoBdd !== null) {
            $this->errors['error']["pseudoEmpty"] = 'Pseudo manquant ou déjà pris';
        } elseif ($email === null || $emailBdd !== null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
            $this->errors['error']["emailEmpty"] = 'Champs email vide ou est déjà présente en bdd';
        } elseif (mb_strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
            $this->errors['error']["passwordEmpty"] = 'Le mot de passe doit avoir des minuscule-majuscule-chiffres-caractères et être inférieur à 8 caractères';
        }elseif($password !== $passwordConfirmation || empty($password)){
            $this->errors['error']["passwordEmpty"] = 'Les champs mot de passe et mot de passe de confirmation sont vide ou ne correspond pas';
        }
        if ($token->compareTokens($session->getSessionName('token'), $dataPost->get('token')) !== false) {
            $this->errors['error']['formRgister'] = "Formulaire incorrect";
        }
        if (empty($this->errors)) {
            $session->setSession('user', $email);
            $this->userRepository->create($email, $pseudo, $password);
            $this->success['success'] = 'Utilisateur est bien inscrit';
            return $this->success;
        }
        return $this->errors;
    }
    public function findPasswordByUser(string $user): string
    {
        return $this->userRepository->findPasswordByUserOrEmail($user);
    }
    public function checkPassword(Session $session, Request $request, Token $token, string $user)
    {
        $idUser = $session->getSession()['idUser'] ?? null;
        $pass = $this->findPasswordByUser($user);
        $post = $request->getPost() ?? null;
        $password = $post->get('password') ?? null;
        $passwordConf = $post->get('passwordConfirmation') ?? null;
        if (empty($password) || $password !== $passwordConf || mb_strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
            $this->errors['error']["passwordEmpty"] = 'Mot de passe invalid, mot de passe doit être identique à celui de confirmation, supérieur à 8 caractères et doit avoir minuscule-majuscule-chiffres-caractères ';
        } elseif (empty($passwordConf) || password_verify($password, $pass)) {
            $this->errors['error']["passwordConfEmpty"] = 'Mot de passe de confirmation absent ou ne correspond pas à celui en bdd';
        }
        if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
            $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
        }
        if (empty($this->errors)) {
            $this->success['success']['send'] = 'Mot de passe  bien mis à jour:';
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
        $userBdd = $this->findByUserEmail($session->getSessionName('user'));
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
            $this->userRepository->update($email, $userName);
            $session->setSession('user', $userName);
            $session->setSession('mail', $email);
            return $this->success;
        }
        return $this->errors;
    }
}
