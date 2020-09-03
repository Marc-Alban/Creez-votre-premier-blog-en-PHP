<?php
declare(strict_types=1);
namespace App\Model\Repository;
class DatabaseProperties
{
    private array $ini;
    private $DbHost;
    private $DbName;
    private $DbUser;
    private $DbPass;
    public function __construct()
    {
        $this->ini = parse_ini_file(ROOT.'config.ini', false);
        $this->DbHost  = $this->ini['DbHost'];
        $this->DbName  = $this->ini['DbName'];
        $this->DbUser  = $this->ini['DbUser'];
        $this->DbPass  = $this->ini['DbPass'];
    }
    public function connect(): array
    {
        $path = [
            'dsn' => 'mysql:host='.$this->DbHost.';dbname='.$this->DbName.'',
            'user' => $this->DbUser,
            'pass' => $this->DbPass
        ];
        return $path;
    }
}