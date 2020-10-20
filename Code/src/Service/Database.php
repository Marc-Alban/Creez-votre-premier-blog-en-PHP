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
    private $database;
    private $pdoOptions;

    public function __construct(ConfigProperties $configProperties)
    {
        $this->configProperties = $configProperties;
        $this->dsn = $this->configProperties->connect();
        try {
            $this->pdoOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
            $this->database = new PDO($this->dsn['dsn'], $this->dsn['user'], $this->dsn['pass'], $this->pdoOptions);
        } catch (Exception $e) {
            throw new Exception("Probleme avec la bdd");
            $e->getMessage();
        }
    }


    public function getPdo(): PDO
    {
        return $this->database;
    }
}
