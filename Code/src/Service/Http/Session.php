<?php
declare(strict_types=1);

namespace App\Service\Http;

class Session
{
    private $session;
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->session = $_SESSION; 
        }
    }
    public function getSession():array
    {
        return $this->session;
    }
    public function getSessionName($name): ?string
    {
        if(isset($this->session[$name])) {
            return $this->session[$name];
        }
        return null;
    }
    public function setSession($name, $value): void
    {
        $this->session[$name] = $value;
    }
    public function sessionDestroy(): void
    {
        session_destroy();
    }
}
