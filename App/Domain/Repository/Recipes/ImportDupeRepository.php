<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class ImportDupeRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function importDupe(int $planId, int $recipeId): bool {
        return (bool)$this->db->queryFirstField("select count(id) total from recipe_urls where planNum = %i and recipeNum = %i", $planId, $recipeId);
    }
}