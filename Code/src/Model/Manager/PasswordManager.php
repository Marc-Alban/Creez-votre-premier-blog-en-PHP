<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Service\Security\Token;
use App\Model\Repository\PasswordRepository;


final class PasswordManager
{
    private Token $token;
    private PasswordRepository $PasswordRepository;

    public function __construct(array $datas)
    {
        $this->PasswordRepository = $datas['repository']['repoPage'];
        $this->token = $datas['token'];
    }

    public function getPassBdd(string $user): ?string
    {
        return $this->PasswordRepository->getPassword($user);
    }

    public function verifPass(array $data, string $user)
    {
        $action = $data['get']['action'] ?? null;
        $idUser = $data['session']['idUser'] ?? null;
        $pass = $this->getPassBdd($user);


        $errors = $data["session"]["errors"] ?? null;
        unset($data["session"]["errors"]);

        $succes = $data["session"]["succes"] ?? null;
        unset($data["session"]["succes"]);

        if (isset($data['post']) && $action === "modifPass") {

            $password = $data["post"]['password'] ?? null;
            $passwordConf = $data["post"]['passwordConfirmation'] ?? null;

            if (empty($password)) {
                $errors['error']["passwordEmpty"] = 'Veuillez mettre un mot de passe ';
            }  else if (empty($passwordConf)) {
                $errors['error']["passwordConfEmpty"] = 'Veuillez mettre un mot de passe de confirmation';
            } else if ($password !== $passwordConf) {
                $errors['error']['passWrong'] = "Le mot de passe n'est pas identique à celui de confirmation";
            } else if (strlen($password) < 8){
                $errors['error']['lenghtWrong'] = "Le mot de passe doit être plus grands que 8 caractères";
            } else if (!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,}$#', $password)) {
                $errors['error']['passNotCorrect'] = 'Mot de passe non conforme, doit avoir minuscule-majuscule-chiffres-caractères';
            }else if (password_verify($password, $pass)) {
                $errors['error']['passwordWrong'] = 'Mot de passe identique à celui d\'avant';
            }
            
            /************************************Token Session************************************************* */
            if ($this->token->compareTokens($data) !== null) {
                $errors['error']['form'] = "Formulaire incorrect";
            }
            /************************************End Token Session************************************************* */

            if (empty($errors)) {
                $succes['succes']['send'] = 'Mot de passe  bien mis à jour:';
                $passhash = password_hash($password, PASSWORD_BCRYPT);
                $this->PasswordRepository->updatePassBdd($passhash, $idUser);
                return $succes;
            }
            return $errors;
        }
        return null;
    }
}
