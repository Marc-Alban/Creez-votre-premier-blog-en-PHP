<?php
declare(strict_types=1);
namespace App\Service\Http;

use App\Service\Http\Parameter;

class Request
{
    private $get;
    private $post;
    private $file;

    public function __construct()
    {
        $this->get = new Parameter($_GET);
        $this->post = new Parameter($_POST);
        $this->file = $_FILES;
    }

    /**
     * @return mixed
     */
    public function getGet(): Parameter
    {
        return $this->get;
    }

    /**
     * @return mixed
     */
    public function getPost(): Parameter
    {
        return $this->post;
    }

    /**
    * @return mixed
    */
    public function getFile(): array
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function setGet(): void
    {
        $this->get;
    }

    /**
     * @return mixed
     */
    public function setPost(): void
    {
        $this->post;
    }

    /**
     * @return mixed
     */
    public function setFile(): void
    {
        $this->file;
    }
}
