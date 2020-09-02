<?php
declare(strict_types = 1);
namespace App\Service;
use \PDO;
use App\Model\Repository\DatabaseProperties;

final class Database
{
    private $database;
    private $Dsn;
    private DatabaseProperties $databaseProperties;

    public function __construct()
    {
        $this->databaseProperties = new DatabaseProperties;
        $this->Dsn = $this->databaseProperties->connect();
    }

    public function getPdo(): PDO
    {
        $this->database = new PDO($this->Dsn);
        return $this->database;
    }
}
