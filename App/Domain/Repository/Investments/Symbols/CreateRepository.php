<?php

namespace App\Domain\Repository\Investments\Symbols;

use MeekroDB;

final class CreateRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function create(array $args): int {
        $this->db->insert('investment_symbols', $args);

        if (!$this->db->affectedRows()) {
            return $this->db->affectedRows();
        } else {
            return $this->db->insertId();
        }
    }
}