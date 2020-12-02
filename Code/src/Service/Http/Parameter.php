<?php
declare(strict_types=1);
namespace App\Service\Http;

class Parameter
{
    private $parameter;
    public function __construct($parameter)
    {
        $this->parameter = $parameter;
    }
    /**
     * Method used to retrieve the name as a parameter in the GET superglobal
     *
     * @param string $name
     * @return string|null
     */
    public function getName(string $name = null): ?string
    {
        if (isset($this->parameter[$name])) {
            return htmlspecialchars(trim($this->parameter[$name]), ENT_QUOTES);
        }
        return null;
    }
}
