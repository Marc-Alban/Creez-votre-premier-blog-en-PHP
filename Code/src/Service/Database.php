<?php
declare (strict_types = 1);
namespace App\Service;
use \PDO;

final class Database 
{
    private static $database = null;
    private static $DSN;
    private static $dbUser;
    private static $dbPass;
    private static $ini;

    public static function getPdo(): PDO
    {
        self::$ini = parse_ini_file(ROOT.'config.ini', false);
        self::$DSN = self::$ini['dsn'];
        self::$dbUser = self::$ini['dbUser'];
        self::$dbPass = self::$ini['dbPass'];
        if (self::$database === null) {
            $pdoOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
            self::$database = new PDO(self::$DSN, self::$dbUser, self::$dbPass, $pdoOptions);
        }
        return self::$database;
    }
}