<?php
declare(strict_types=1);
namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use App\Service\Http\Parameter;
use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Service\Security\Token;

final class UserManager
{
    private UserRepository $userRepository;
    private $errors = null;
    private $succes = null;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function findByIdUser(int $user): string
    {
        return $this->userRepository->findById($user);
    }
    public function findAllUser(): ?User
    {
        return $this->userRepository->findAll();
    }
    public function checkUser(Session $session, Token $token, Request $request, string $action = null): ?array
    {
        $post = $request->getPost() ?? null;
        if ($action === "connexion") {
            $email = $post->get('email') ?? null;
            $password = $post->get('password') ?? null;
            $passwordBdd = $this->userRepository->findPasswordByUserOrEmail(null, $email);
            if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
                $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } elseif (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email) || $email !== $this->userRepository->findByEmail($email)) {
                $this->errors['error']["emailEmpty"] = 'E-mail invalid ou inexistant ';
            } elseif (empty($password) || !password_verify($password, $passwordBdd)) {
                $this->errors['error']["passwordEmpty"] = 'Mot de passe incorrect';
            }
            if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
                $this->errors['error']['formRgister'] = "Formulaire incorrect";
            }
            if (empty($this->errors)) {
                $this->succes['succes']['send'] = 'Content de vous revoir : ' . $this->userRepository->findById();
                $session->setSession('user', $this->userRepository->findById());
                $session->setSession('userAdmin', $this->userRepository->findById());
                $session->setSession('idUser', $this->userRepository->findById());
                return $this->succes;
            }
            return $this->errors;
        }
        return null;
    }
    public function userSignIn(Session $session, Token $token, Request $request, string $action = null): ?array
    {
        $dataPost = $request->getPost() ?? null;
        if ($action === "inscription") {
            $pseudo = $dataPost->get('userName') ?? null;
            $email = $dataPost->get('email') ?? null;
            $emailBdd = $this->userRepository->findByEmail(mb_strtolower($email));
            $password =  $dataPost->get('password') ?? null;
            $passwordConfirmation = $dataPost->get('passwordConfirmation') ?? null;
            if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
                $this->errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } elseif (empty($pseudo)) {
                $this->errors['error']["pseudoEmpty"] = 'Veuillez mettre un pseudo ';
            } elseif (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email) || $email === $emailBdd) {
                $this->errors['error']["emailEmpty"] = 'Mail invalide ou est déjà présente en bdd';
            } elseif (empty($password) || mb_strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password) || $password !== $passwordConfirmation) {
                $this->errors['error']["passwordEmpty"] = 'Mot de passe invalid, doit avoir minuscule-majuscule-chiffres-caractères ';
            }
            if ($token->compareTokens($session->getSessionName('token'), $dataPost->get('token')) !== false) {
                $this->errors['error']['formRgister'] = "Formulaire incorrect";
            }
            if (empty($this->errors)) {
                $session->setSession('user', $pseudo);
                $this->userRepository->create($dataPost);
                $this->succes['succes']['send'] = 'Utilisateur est bien inscrit';
                return $this->succes;
            }
            return $this->errors;
        }
        return null;
    }
    public function findPasswordByUser(string $user): ?string
    {
        return $this->userRepository->findPasswordByUserOrEmail($user);
    }
    public function checkPassword(Session $session, Request $request, Token $token, string $action, string $user)
    {
        $idUser = $session->getSession()['idUser'] ?? null;
        $pass = $this->findPasswordByUser($user);
        $post = $request->getPost() ?? null;
        if ($action === "modifPass") {
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
                $this->succes['succes']['send'] = 'Mot de passe  bien mis à jour:';
                $passhash = password_hash($password, PASSWORD_BCRYPT);
                $this->userRepository->updatePassword($passhash, $idUser);
                return $this->succes;
            }
            return $this->errors;
        }
        return null;
    }
    
    public function checkForm(Session $session, Request $request, Token $token, string $action = null)
    {
        $post = $request->getPost() ?? null;
        if ($action === "sendDatasUser") {
            $email = $post->get('email') ?? null;
            $userName = $post->get('userName') ?? null;
            $userBdd = $this->findAllUser()->getUserName();
            $idUser = $this->findAllUser()->getIdUser();
            if (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
                $this->errors['error']["emailEmpty"] = 'L\'adresse e-mail est invalide" ';
            } elseif (empty($userName)) {
                $this->errors['error']["userEmpty"] = 'Veuillez mettre un utilisateur';
            }
            if ($token->compareTokens($session->getSessionName('token'), $post->get('token')) !== false) {
                $this->errors['error']['tokenEmpty'] = 'Formulaire incorrect';
            }
            if (empty($this->errors)) {
                $this->succes['succes']['send'] = 'Utilisateur bien mis à jour:';
                $this->userRepository->update($idUser, $email, $userName);
                $session->setSession('user', $userBdd);
                $session->setSession('userAdmin', $this->findAllUser()->getActivated());
                $session->setSession('idUser', $this->findAllUser()->getIdUser());
                return $this->succes;
            }
            return $this->errors;
        }
        return null;
    }
}
