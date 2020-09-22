<?php

declare(strict_types=1);

namespace App\Service;

use \PDO;
use App\Service\ConfigProperties;
use Exception;

final class Database
{
    private ConfigProperties $configProperties;
    private array $dsn;

    public function __construct(ConfigProperties $configProperties)
    {
        $this->configProperties = $configProperties;
        $this->dsn = $this->configProperties->connect();
        ini_set('display_errors', '1');
        try {
            $this->database = new PDO($this->dsn['dsn'], $this->dsn['user'], $this->dsn['pass']);
        } catch (Exception $e) {
                $e->getMessage();
        }
    }


    public function getPdo(): PDO
    {
        return $this->database;
    }
}
