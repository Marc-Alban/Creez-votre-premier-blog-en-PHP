<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Service\Http\Session;

final class UpdatePostManager
{
    private Session $session;

    public function __construct(array $datas)
    {
        $this->session = $datas['session'] ?? null;
    }

}