<?php
declare(strict_types=1);

namespace App\Service\Http;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public function getSession($name): ?array
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return null;
    }
    public function setSession($name, $instance): void
    {
        $_SESSION["$name"] = $instance;
    }
    public function sessionDestroy(): void
    {
        session_destroy();
    }
}
