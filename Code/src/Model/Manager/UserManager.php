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

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function findUser(int $user): string
    {
        return $this->userRepository->getUser($user);
    }
    public function verifUser(Session $session,Token $token,Request $request,string $action = null): ?array
    {
        $post = $request->getPost() ?? null;
        $tokenForm = $request->getPost()->get('token') ?? null;
        $errors = $session["errors"] ?? null;
        unset($session["errors"]);
        $succes = $session["succes"] ?? null;
        unset($session["succes"]);
        if (isset($post) && $action === "connexion") {
            $email = $post->get('email') ?? null;
            $password = $post->get('password') ?? null;
            $passwordBdd = $this->userRepositorysitory->getPassword($email);
            if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
                $errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } else if (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email) || $email !== $this->userRepositorysitory->getEmailBdd($email) ) {
                $errors['error']["emailEmpty"] = 'E-mail invalid ou inexistant ';
            } else if (empty($password) || !password_verify($password, $passwordBdd)) {
                $errors['error']["passwordEmpty"] = 'Mot de passe incorrect';
            }
            /************************************Token Session************************************************* */
            if ($token->compareTokens($session,$tokenForm) !== null) {
                $errors['error']['formRgister'] = "Formulaire incorrect";
            }
            /************************************End Token Session************************************************* */
            if (empty($errors)) {
                $succes['succes']['send'] = 'Content de vous revoir : ' . $this->userRepositorysitory->getUser();
                $session->setParamSession('user', $this->userRepositorysitory->getUser());
                $session->setParamSession('userAdmin', $this->userRepositorysitory->getIdUser());
                $session->setParamSession('idUser', $this->userRepositorysitory->getIdUser());
                header('Location: /?page=home');
                return $succes;
            }
            return $errors;
        }
        return null;
    }
    public function userSignIn(Session $session,Token $token,Request $request,string $action = null): ?array
    {
        $post = $request->getPost() ?? null;
        $errors = $session["errors"] ?? null;
        unset($session["errors"]);
        $succes = $session["succes"] ?? null;
        unset($session["succes"]);
        if (isset($post) && $action === "inscription") {
            $pseudo = $post->get('userName') ?? null;
            $email = $post->get('email') ?? null;
            $emailBdd = $this->userRepositorysitory->getEmailBdd(strtolower($email));
            $password =  $post->get('password') ?? null;
            $passwordConfirmation = $post->get('passwordConfirmation') ?? null;
            if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
                $errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } else if (empty($pseudo)) {
                $errors['error']["pseudoEmpty"] = 'Veuillez mettre un pseudo ';
            } else if (empty($email)) {
                $errors['error']["emailEmpty"] = 'Veuillez mettre un mail ';
            } else if (!preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
                $errors['error']['emailWrong'] = "L'adresse e-mail est invalide";
            } else if ($emailBdd !== null && $email === $emailBdd) {
                $errors['error']['emailFalse'] = "L'adresse e-mail est déjà présente en bdd";
            } else if (empty($password) || strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
                $errors['error']["passwordEmpty"] = 'Mot de passe invalid, doit avoir minuscule-majuscule-chiffres-caractères ';
            } else if ($password !== $passwordConfirmation) {
                $errors['error']['passwordWrong'] = 'Mot de passe et mot de passe de confirmation ne corresponde pas.. ';
            } 
            if ($token->compareTokens($session,$post->get('token')) !== null) {
                $errors['error']['formRgister'] = "Formulaire incorrect";
            }
            if (empty($errors)) {
                $session->setParamSession('user', $pseudo);
                $this->userRepositorysitory->createUser();
                $succes['succes']['send'] = 'Utilisateur est bien inscrit';
                return $succes;
            }
            return $errors;
        }
        return null;
    }
    public function getPassBdd(string $user): ?string
    {
        return $this->userRepository->getPassword($user);
    }
    public function verifPass(Session $session,Request $request,Token $token,string $action,string $user)
    {
        $idUser = $session['idUser'] ?? null;
        $pass = $this->getPassBdd($user);
        $post = $this->request->getPost() ?? null;
        $errors = $session["errors"] ?? null;
        unset($session["errors"]);
        $succes = $session["succes"] ?? null;
        unset($session["succes"]);
        if (isset($post) && $action === "modifPass") {
            $password = $post['password'] ?? null;
            $passwordConf = $post['passwordConfirmation'] ?? null;
            if (empty($password) || $password !== $passwordConf || strlen($password) < 8 || !preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
                $errors['error']["passwordEmpty"] = 'Mot de passe invalid, mot de passe doit être identique à celui de confirmation, supérieur à 8 caractères et doit avoir minuscule-majuscule-chiffres-caractères ';
            } else if (empty($passwordConf) || password_verify($password, $pass)) {
                $errors['error']["passwordConfEmpty"] = 'Mot de passe de confirmation absent ou ne correspond pas à celui en bdd';
            }
            if ($token->compareTokens($session,$post->get('token')) === true) {
                $errors['error']['tokenEmpty'] = 'Formulaire incorrect';
            }
            if (empty($errors)) {
                $succes['succes']['send'] = 'Mot de passe  bien mis à jour:';
                $passhash = password_hash($password, PASSWORD_BCRYPT);
                $this->userRepository->updatePassBdd($passhash, $idUser);
                return $succes;
            }
            return $errors;
        }
        return null;
    }
    private function sendMail(string $message, string $mail): void
    {
        $message = $message;
        $entete  = 'MIME-Version: 1.0' . "\r\n";
        $entete .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $entete .= 'From: ' . $mail . "\r\n";
        $message = require_once ROOT . 'templates\frontoffice\mail.html.twig';
        mail('millet.marcalban@gmail.com', 'Envoi depuis page home', $message, $entete);
    }
    public function verifMail(Session $session,Token $token ,Request $request, string $action =null): ?array
    {
        $post = $request->getPost() ?? null;
        if (isset($post) && $action === "send") {
            $mail = $post->get('mail') ?? null;
            $name = $post->get('name') ?? null;
            $lastName = $post->get('lastName') ?? null;
            $message = $post->get('message') ?? null;
            $session = $session->getSession()['token'] ?? null;
            if (empty($mail) && empty($message) && empty($name) && empty($lastName)) {
                $errors['error']['allEmpty'] = "Veuillez remplir le formulaire";
            } else if (empty($name)) {
                $errors['error']['nameEmpty'] = "Veuillez mettre un nom";
            } else if (empty($lastName)) {
                $errors['error']['lastNameEmpty'] = "Veuillez mettre un prénom";
            } else if (empty($mail) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $mail)) {
                $errors['error']['mailEmpty'] = "Veuillez mettre un mail valide";
            } else if (empty($message)) {
                $errors['error']['messageEmpty'] = "Veuillez mettre un message";
            }
            if ($token->compareTokens($session,$post->get('token')) === true) {
                $errors['error']['tokenEmpty'] = 'Formulaire incorrect';
            }
            if (empty($errors)) {
                $this->sendMail($message, $mail);
                $succes['succes']['send'] = 'Votre message a bien été envoyé.';
                return $succes;
            }
            return $errors;
        }
        return null;
    }
    public function getDataUser(): ?User
    {
        return $this->userRepository->getAllFromUser();
    }
    public function verifForm(Session $session,Request $request,Token $token,string $action = null)
    {
        $post = $request->getPost() ?? null;
        $errors = $session["errors"] ?? null;
        unset($session["errors"]);
        $succes = $session["succes"] ?? null;
        unset($session["succes"]);
        if (isset($post) && $action === "sendDatasUser") {
            $email = $post->get('email') ?? null;
            $userName = $post->get('userName') ?? null;
            $userBdd = $this->getDataUser()->getUserName();
            $idUser = $this->getDataUser()->getIdUser();
            if (empty($email) || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
                $errors['error']["emailEmpty"] = 'L\'adresse e-mail est invalide" ';
            }else if (empty($userName)) {
                $errors['error']["userEmpty"] = 'Veuillez mettre un utilisateur';
            }
            if ($token->compareTokens($session,$post->get('token')) === true) {
                $errors['error']['tokenEmpty'] = 'Formulaire incorrect';
            }
            if (empty($errors)) {
                $succes['succes']['send'] = 'Utilisateur bien mis à jour:';
                $this->userRepository->updateUserBdd($idUser,$email,$userName);
                $session->setParamSession('user', $userBdd);
                $session->setParamSession('userAdmin', $this->getDataUser()->getActivated());
                $session->setParamSession('idUser', $this->getDataUser()->getIdUser());
                header('Location: /?page=dashboard');
                return $succes;
            }
            return $errors;
        }
        return null;
    }
}
