<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class ListSingleRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function listSingle(int $id): array {
        $recipe['details'] = $this->db->queryFirstRow("
            SELECT * FROM recipes WHERE id = %i
        ", $id);
        
        $recipe['ingredients'] = $this->db->query("
            SELECT * FROM recipe_ingredients WHERE recipes_id = %i
        ", $id);
        
        $recipe['instructions'] = $this->db->query("
            SELECT * FROM recipe_instructions WHERE recipes_id = %i ORDER BY `order` ASC
        ", $id);

        $recipe['side']['details'] = $this->db->queryFirstRow("
            SELECT * FROM sides WHERE recipes_id = %i
        ", $id);
        
        if (isset($recipe['side']['details']['id'])) {
            $recipe['side']['ingredients'] = $this->db->query("
                SELECT * FROM side_ingredients WHERE sides_id = %i
            ", $recipe['side']['details']['id']);
            
            $recipe['side']['instructions'] = $this->db->query("
                SELECT * FROM side_instructions WHERE sides_id = %i ORDER BY `order` ASC
            ", $recipe['side']['details']['id']);
        }

        return (array)$recipe;
    }
}