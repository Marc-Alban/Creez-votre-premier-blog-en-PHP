<?php

declare(strict_types=1);

namespace App\Service;

use \PDO;
use App\Service\ConfigProperties;

final class Database
{
    private PDO $database;
    private array $dsn;
    private ConfigProperties $configProperties;

    public function __construct(ConfigProperties $configProperties)
    {
        $this->configProperties = $configProperties;
        $this->dsn = $this->configProperties->connect();
        $this->database = new PDO($this->dsn['dsn'], $this->dsn['user'], $this->dsn['pass']);
        
    }

    public function getPdo(): PDO
    {
        return $this->database ;
    }
}
