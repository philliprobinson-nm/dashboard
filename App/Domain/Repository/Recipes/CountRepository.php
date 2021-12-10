<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class CountRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function count(): int {
        return (int)$this->db->queryFirstField("select count(id) from recipes");
    }
}