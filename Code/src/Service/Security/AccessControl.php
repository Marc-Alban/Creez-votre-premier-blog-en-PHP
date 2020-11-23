<?php
declare(strict_types=1);
namespace App\Service\Security;

use App\Model\Entity\User;
use App\Service\Http\Request;
use App\Service\Http\Session;

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
        $email = $dataPost->getName('email') ?? null;
        $password = $dataPost->getName('password') ?? null;
        $emailBdd = null;
        $passwordBdd = null;
        if ($userbdd !== null) {
            $emailBdd = $userbdd->getEmail();
            $passwordBdd = $userbdd->getPasswordUser();
        }
        if (empty($email) || $emailBdd === null || !preg_match(" /^.+@.+\.[a-zA-Z]{2,}$/ ", $email) || empty($password) || !password_verify($password, $passwordBdd)) {
            $this->errors['error']["notGood"] = 'Identifiants incorrect';
        }
        if (empty($this->errors)) {
            if ($userbdd->getActivated() === 0) {
                $session->setSession('user', $email);
            } elseif ($userbdd->getActivated() === 1) {
                $session->setSession('admin', $email);
            }
            return null;
        }
        return $this->errors;
    }
    /**
     * Get the admin session and reset this, after create a user session
     *
     * @param Session $session
     * @return void
     */
    public function IsAdmin(Session $session): void
    {
        $admin = $session->getSessionName('admin');
        $session->setSession('user', $admin);
        $session->sessionDestroyName('admin');
    }
}
