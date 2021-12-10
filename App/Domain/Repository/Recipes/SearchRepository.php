<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class SearchRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function search(string $term): array {
        return (array)$this->db->queryFirstColumn("select title from recipes where title like %ss", $term);
    }
}