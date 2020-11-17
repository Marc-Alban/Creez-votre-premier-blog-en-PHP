<?php
declare(strict_types = 1);
namespace App\Service\Security;

class Token
{
    private $token;
    /**
     * Create a token in session
     *
     * @return string
     */
    public function createSessionToken(): string
    {
        $this->token = bin2hex(random_bytes(32));
        return $this->token;
    }
    /**
     * Function that compares the token in session and the token of a form passed as a parameter
     *
     * @param string $sessionToken
     * @param string $token
     * @return boolean
     */
    public function compareTokens(string $sessionToken, string $token): bool
    {
        if (empty($sessionToken) || empty($token) || $sessionToken !== $token) {
            return true;
        }
        return false;
    }
}
