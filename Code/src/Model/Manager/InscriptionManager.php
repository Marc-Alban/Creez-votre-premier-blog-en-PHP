<?php
declare(strict_types=1);
namespace App\Model\Manager;
use App\Model\Repository\InscriptionRepository;
use App\Service\Http\Session;
use App\Service\Security\Token;

class InscriptionManager
{
    private InscriptionRepository $inscriptionRepository;
    private Token $token;
    private Session $session;
    public function __construct(InscriptionRepository $inscriptionRepository, Token $token)
    {
        $this->inscriptionRepository = $inscriptionRepository;
        $this->token = $token;
    }

    public function userSignIn(array $data): ?array
    {
        $action = $data['get']['action'] ?? null;

        $errors = $data["session"]["errors"] ?? null;
        unset($data["session"]["errors"]);

        $succes = $data["session"]["succes"] ?? null;
        unset($data["session"]["succes"]);

        if (isset($data['post']['Register']) && $action === "inscription") {

            $pseudo = $data["post"]['userName'] ?? null;
            $email = $data["post"]['email'] ?? null;
            $password = $data["post"]['password'] ?? null;
            $passwordConfirmation = $data["post"]['passwordConfirmation'] ?? null;

            if (empty($pseudo) && empty($email) && empty($password) && empty($passwordConfirmation)) {
                $errors['error']["formEmpty"] = 'Veuillez mettre un contenu';
            } else if (empty($pseudo)) {
                $errors['error']["pseudoEmpty"] = 'Veuillez mettre un pseudo ';
            } else if (empty($password)) {
                $errors['error']["passwordEmpty"] = 'Veuillez mettre un mot de passe';
            } else if ($password !== $passwordConfirmation) {
                $errors['error']['passwordWrong'] = 'Mot de passe et mot de passe de confirmation ne corresponde pas.. ';
            }

            /************************************Token Session************************************************* */
            // if ($this->token->compareTokens($data['post']['token']) !== null) {
            //     $errors['error']['token'] = "Formulaire incorrect";
            // }
            /************************************End Token Session************************************************* */
                // var_dump($data['post']['token']);
                // die();

            if (empty($errors)) {
                $this->session->setParamSession('user', $pseudo);
                $succes['succes'] = "Connexion réussie";
                return $succes;
            }
            return $errors;
        }
        return null;
    }
}