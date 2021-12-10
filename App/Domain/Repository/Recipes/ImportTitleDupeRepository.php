<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class ImportTitleDupeRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function importTitleDupe(string $title): bool {
        return (bool)$this->db->queryFirstField("select count(id) total from recipe_urls where title = %s", $title);
    }
}