<?php
declare(strict_types=1);
namespace App\Service\Http;
use App\Service\Http\Parameter;
class Request
{
    private $get;
    private $post;

    public function __construct()
    {
        $this->get = new Parameter($_GET);
        $this->post = new Parameter($_POST);
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

}