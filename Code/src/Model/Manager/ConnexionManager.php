<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Repository\UserRepository;
use App\Service\Http\Session;
use App\Service\Security\Token;

class ConnexionManager
{

    private UserRepository $userRepository;
    private Token $token;
    private Session $session;

    public function __construct(array $dataManager)
    {
        $this->userRepository = $dataManager['repository']['repoAdd'];
        $this->token = $dataManager['token'];
        $this->session = $dataManager['session'];
    }


    public function verifUser(array $data): ?array
    {
        $action = $data['get']['action'] ?? null;

        $errors = $data["session"]["errors"] ?? null;
        unset($data["session"]["errors"]);

        $succes = $data["session"]["succes"] ?? null;
        unset($data["session"]["succes"]);

        if (isset($data['post']) && $action === "connexion") {

            $email = $data["post"]['email'] ?? null;
            $password = $data["post"]['password'] ?? null;
            $passwordBdd = $this->userRepository->getPassword($email);
            if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
                $errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } else if (empty($email)) {
                $errors['error']["emailEmpty"] = 'Veuillez mettre un mail ';
            }else if (!preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
                $errors['error']['emailWrong'] = "L'adresse e-mail est invalide";
            }else if ($email !== $this->userRepository->getEmailBdd($email)) {
                $errors['error']["emailFalse"] = 'E-mail invalid ou inexistante';
            }else if (empty($password)) {
                $errors['error']["passwordEmpty"] = 'Veuillez mettre un mot de passe';
            } else if (!password_verify($password, $passwordBdd)) {
                $errors['error']['passwordWrong'] = 'Mot de passe incorrect.';
            }

            /************************************Token Session************************************************* */
            if ($this->token->compareTokens($data) !== null) {
                $errors['error']['formRgister'] = "Formulaire incorrect";
            }
            /************************************End Token Session************************************************* */

            if (empty($errors)) {
                $succes['succes']['send'] = 'Content de vous revoir : '. $this->userRepository->getUser();
                $this->session->setParamSession('user', $this->userRepository->getUser());
                $this->session->setParamSession('idUser', $this->userRepository->getIdUser());
                header('Location: /?page=home');               
                return $succes;
            }
            return $errors;
        }
        return null;
    }
}
