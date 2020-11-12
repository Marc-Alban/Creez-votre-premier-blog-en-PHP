<?php
declare(strict_types=1);
namespace App\Service\Security;

use App\Service\Http\Request;
use App\Service\Http\Session;
use App\Model\Entity\User;


class AccessControl
{
    private array $errors;
    /**
     * user role verification
     *
     * @param Session $session
     * @param Request $request
     * @param User|null $userbdd
     * @return array|null
     */
    public function userAction(Session $session, Request $request, ?User $userbdd): ?array
    {
        $dataPost = $request->getPost();
        $email = $dataPost->get('email') ?? null;
        $password = $dataPost->get('password') ?? null;
        $emailBdd = null;
        $passwordBdd = null;
        if($userbdd !== null){
            $emailBdd = $userbdd->getEmail();
            $passwordBdd = $userbdd->getPasswordUser();
        }
        if (empty($email) || $emailBdd === null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email) || empty($password) || !password_verify($password, $passwordBdd)) {
            $this->errors['error']["notGood"] = 'Identifiants incorrect';
        }
        if(empty($this->errors)){
            if($userbdd->getActivated() === 0){
                $session->setSession('user', $email);
            }else if($userbdd->getActivated() === 1){
                $session->setSession('userAdmin', $email);
            }
            return null;
        }
        return $this->errors;
    }
}
