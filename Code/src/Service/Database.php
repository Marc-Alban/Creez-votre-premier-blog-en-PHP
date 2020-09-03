<?php

declare(strict_types=1);

namespace App\Service;

use \PDO;
use App\Model\Repository\DatabaseProperties;

final class Database
{
    private PDO $database;
    private array $dsn;
    private DatabaseProperties $databaseProperties;

    public function __construct()
    {
        $this->databaseProperties = new DatabaseProperties();
        $this->dsn = $this->databaseProperties->connect();
    }

    public function getPdo(): PDO
    {
        $this->database = new PDO($this->dsn['dsn'], $this->dsn['user'], $this->dsn['pass']);
        return $this->database;
    }
}
