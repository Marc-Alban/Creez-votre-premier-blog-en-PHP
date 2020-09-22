<?php
declare(strict_types=1);
namespace App\Service;
use \Exception;
class ConfigProperties 
{
    private array $ini;
    private $DbHost;
    private $DbName;
    private $DbUser;
    private $DbPass;
    public function __construct()
    {
        try{
            $this->ini = parse_ini_file(ROOT.'config.ini', false);
        }catch(Exception $e) {
            die('Erreur : Problème avec le fichier de config ' );
        }
        $this->DbHost  = $this->ini['DbHost'];
        $this->DbName  = $this->ini['DbName'];
        $this->DbUser  = $this->ini['DbUser'];
        $this->DbPass  = $this->ini['DbPass'];
    }
    public function connect(): array
    {
        return [
            'dsn' => 'mysql:host='.$this->DbHost.';dbname='.$this->DbName.'',
            'user' => $this->DbUser,
            'pass' => $this->DbPass
        ];
        
    }
}