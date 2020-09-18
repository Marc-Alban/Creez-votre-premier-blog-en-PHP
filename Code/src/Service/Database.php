<?php

declare(strict_types=1);

namespace App\Service;

use \PDO;
use \Exception;
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
            try{
            $this->database = new PDO($this->dsn['dsn'], $this->dsn['user'], $this->dsn['pass']);
            }catch(Exception $e) {
                die('Erreur : Problème avec la connexion de la base de donnée ' );
            }
    }


    public function getPdo(): PDO
    {
        return $this->database;
    }
}
