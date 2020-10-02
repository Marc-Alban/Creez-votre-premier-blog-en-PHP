<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Service\Http\Session;
use App\Model\Entity\User;
use App\Model\Repository\DashboardRepository;
use App\Model\Repository\UserRepository;
use App\Service\Security\Token;

final class DashboardManager
{
    private Session $session;
    private Token $token;
    private DashboardRepository $DashboardRepository;
    private UserRepository $UserRepository;

    public function __construct(array $datas)
    { 
        $this->session = $datas['session'] ?? null;
        $this->DashboardRepository = $datas['repository']['repoPage'];
        $this->UserRepository = $datas['repository']['repoAdd'];
        $this->token = $datas['token'];
    }

    public function getDataUser(): ?User
    {
        return $this->DashboardRepository->getAllFromUser();
    }

    public function verifForm(array $data)
    {
        $action = $data['get']['action'] ?? null;

        $errors = $data["session"]["errors"] ?? null;
        unset($data["session"]["errors"]);

        $succes = $data["session"]["succes"] ?? null;
        unset($data["session"]["succes"]);

        if (isset($data['post']) && $action === "sendDatasUser") {

            $email = htmlentities(trim($data["post"]['email'])) ?? null;
            $userName = htmlentities(trim($data["post"]['userName'])) ?? null;
            $userBdd = $this->getDataUser()->getUserName();
            $idUser = $this->UserRepository->getIdUser();

            if (empty($email)) {
                $errors['error']["emailEmpty"] = 'Veuillez mettre un mail ';
            }else if (!preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email)) {
                $errors['error']['emailWrong'] = "L'adresse e-mail est invalide";
            }
            
            if (empty($userName)) {
                $errors['error']["userEmpty"] = 'Veuillez mettre un utilisateur';
            } 

            /************************************Token Session************************************************* */
            if ($this->token->compareTokens($data) !== null) {
                $errors['error']['form'] = "Formulaire incorrect";
            }
            /************************************End Token Session************************************************* */

            if (empty($errors)) {
                $succes['succes']['send'] = 'Utilisateur bien mis à jour:';
                $this->DashboardRepository->updateUserBdd($data, $idUser);
                $this->session->setParamSession('user', $userBdd);
                $this->session->setParamSession('userAdmin', $this->getDataUser()->getActivated());
                $this->session->setParamSession('idUser', $this->getDataUser()->getIdUser());      
                header('Location: /?page=dashboard');
                return $succes;
            }
            return $errors;
        }
        return null;
    }

}