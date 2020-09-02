<?php
declare(strict_types=1);
namespace App\Model\Repository;
class DatabaseProperties
{
    private $ini;
    private $DbHost;
    private $DbName;
    private $DbUser;
    private $DbPass;

    public function __construct()
    {
        $this->ini = parse_ini_file(ROOT.'config.ini', false);
        $this->DbHost =$this->ini['DbHost'];
        $this->DbName =$this->ini['DbName'];
        $this->DbUser =$this->ini['DbUser'];
        $this->DbPass =$this->ini['DbPass'];
    }

    public function connect(): string
    {
        $dsn = $this->DbHost.$this->DbName.','.$this->DbUser.','.$this->DbPass;
        return $dsn;
    }
}