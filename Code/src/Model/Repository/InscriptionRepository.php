<?php
declare(strict_types=1);
namespace App\Model\Repository;

use App\Service\Database;

class InscriptionRepository
{
    private Database $db;
    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}