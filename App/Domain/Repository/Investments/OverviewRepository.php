<?php

namespace App\Domain\Repository\Investments;

use MeekroDB;

final class OverviewRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }
}