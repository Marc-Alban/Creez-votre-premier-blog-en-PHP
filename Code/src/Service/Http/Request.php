<?php
declare(strict_types=1);
namespace App\Service\Http;

use App\Service\Http\Parameter;

class Request
{
    private $get;
    private $post;
    private $file;
    public $server;
    public function __construct()
    {
        $this->get = new Parameter($_GET);
        $this->post = new Parameter($_POST);
        $this->file = $_FILES;
    }
    /**
     * Using the class parameter returns the superglobal GET
     *
     * @return Parameter
     */
    public function getGet(): Parameter
    {
        return $this->get;
    }
    /**
     * Using the class parameter returns the superglobal POST
     *
     * @return Parameter
     */
    public function getPost(): Parameter
    {
        return $this->post;
    }
    /**
     * Get the superglobal FILE
     *
     * @return array
     */
    public function getFile(): array
    {
        return $this->file;
    }
}
