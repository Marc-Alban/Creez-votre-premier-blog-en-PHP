<?php
declare(strict_types=1);
namespace App\Service;
use Exception;
use \PDO;

// class pour gérer la connection à la base de donnée
class Database
{
    const HOST = 'localhost';
    const USER = 'root';
    const PASSWORD = '';
    const DATABASE = 'blog';
    private static $instance = null;

    public static function getPdo(): PDO
    {
        try{
            if(self::$instance === null)
                self::$instance = new PDO('mysql:host='.self::HOST.';dbname='.self::DATABASE.';',self::USER,self::PASSWORD);
            return self::$instance;
        }catch (Exception $e ){
            die('Erreur : ' . $e->getMessage());
        }
    }
}