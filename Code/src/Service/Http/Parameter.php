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

    public function get($name): ?string
    {
        if(isset($this->parameter[$name]))
        {
            return htmlentities(strip_tags(trim($this->parameter[$name])));
        }
        return null;
    }
    
    public function set($name, $value): void
    {
        $this->parameter[$name] = $value;
    }

}