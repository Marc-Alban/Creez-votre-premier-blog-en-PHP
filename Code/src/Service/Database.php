<?php
declare(strict_types = 1);
namespace App\Service;
use \PDO;
use App\Model\Repository\DatabaseProperties;

final class Database
{
    private $database;
    private DatabaseProperties $Dsn;

    public function __construct(DatabaseProperties $DbInfos)
    {
        $DbInfosDatabase = $DbInfos->connect();
        $this->Dsn = $DbInfosDatabase;
    }
    
    public function getPdo(): PDO
    {
        $pdoOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
        $this->database = new PDO($this->Dsn, $pdoOptions);
        return $this->database;
    }
}
