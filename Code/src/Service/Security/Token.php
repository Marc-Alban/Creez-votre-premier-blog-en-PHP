<?php
declare(strict_types = 1);
namespace App\Service\Security;

use App\Service\Http\Session;

class Token
{
    private $token;

    /************************************Create Token Session************************************************ */
    public function createSessionToken(): string
    {
        $this->token = bin2hex(random_bytes(32));
        return $this->token;
    }
    /************************************End Create Token Session************************************************ */
    /************************************Compare Token Session************************************************ */

    public function compareTokens(Session $session, string $token): bool
    {
        if (empty($session['token']) || empty($token) || $session['token'] !== $token) {
            return true;
        }
        return false;
    }
    /************************************End Compare Token Session************************************************ */
}
