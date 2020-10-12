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

    /************************************getSession************************************************ */
    public function getSession(): array
    {
        return $_SESSION;
    }
    /************************************End getSession************************************************ */
    /************************************setParamSession************************************************ */
    public function setParamSession($name, $instance): array
    {
        $_SESSION["$name"] = $instance;
        return $_SESSION;
    }
    /************************************End setParamSession*********************************************** */
    public function sessionDestroy(): void
    {
        session_destroy();
    }
}
