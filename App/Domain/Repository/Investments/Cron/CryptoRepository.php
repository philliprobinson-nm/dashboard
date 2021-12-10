<?php

namespace App\Domain\Repository\Investments\Cron;

use MeekroDB;

final class CryptoRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function insert(array $args): int {
        $this->db->insert('investment_cryptos_history', $args);

        return $this->db->insertId();
    }

    public function getSymbols(): array {
        return $this->db->queryFirstColumn("select symbol from investment_symbols where type = 'crypto'");
    }

    public function getSymbolId(string $symbol): int {
        return $this->db->queryFirstField("select id from investment_symbols where symbol = %s and type = 'crypto'", $symbol);
    }
}