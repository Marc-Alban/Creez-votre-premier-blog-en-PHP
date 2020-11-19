<?php
declare(strict_types=1);
namespace App\Service\Http;

class Session
{
    private array $session;
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->session = &$_SESSION;
    }
    /**
     * Get Session
     *
     * @return array
     */
    public function getSession():array
    {
        return $this->session;
    }
    /**
     * Get index in session with the name on parameter
     *
     * @param string $name
     * @return string|null
     */
    public function getSessionName(string $name): ?string
    {
        if (isset($this->session[$name])) {
            return $this->session[$name];
        }
        return null;
    }
    /**
     * Create a paramter in session
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setSession(string $name, string $value): void
    {
        $this->session[$name] = $value;
    }
    /**
     * Destroy the session
     *
     * @return void
     */
    public function sessionDestroy(): void
    {
        session_destroy();
    }
    /**
     * Destroy parametre session
     *
     * @param string $name
     * @return void
     */
    public function sessionDestroyName(string $name):void
    {
        unset($this->session[$name]);
    }
}
