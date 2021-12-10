<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class ImportEmealsRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function importEmeals(array $recipe): int {
        $this->db->insert('recipes', [
            'photo' => $recipe['image'] ?? null,
            'title' => $recipe['title'],
            'prep_time' => $recipe['times']['prep'],
            'cook_time' => $recipe['times']['cook'],
            'total_time' => $recipe['times']['total']
        ]);

        $recipe['id'] = $this->db->insertId();
        
        foreach ($recipe['ingredients'] as $r) {
            $this->db->insert('recipe_ingredients', [
            'recipes_id' => $recipe['id'],
            'quantity' => $r['quantity'] ?? null,
            'unit' => $r['unit'] ?? null,
            'name' => $r['name'],
            'info' => $r['info'] ?? null
            ]);
        }

        foreach ($recipe['instructions'] as $key=>$val) {
            $this->db->insert('recipe_instructions', [
            'recipes_id' => $recipe['id'],
            'order' => $key,
            'instruction' => $val
            ]);
        }

        if (!empty($recipe['side_title'])) {
            $this->db->insert('sides', [
            'recipes_id' => $recipe['id'],
            'title' => $recipe['side_title']
            ]);

            $recipe['sides_id'] = $this->db->insertId();
            
            foreach ($recipe['side_ingredients'] as $r) {
                $this->db->insert('side_ingredients', [
                    'sides_id' => $recipe['sides_id'],
                    'quantity' => $r['quantity'] ?? null,
                    'unit' => $r['unit'] ?? null,
                    'name' => $r['name'],
                    'info' => $r['info'] ?? null
                ]);
            }

            foreach ($recipe['side_instructions'] as $key=>$val) {
                $this->db->insert('side_instructions', [
                    'sides_id' => $recipe['sides_id'],
                    'order' => $key,
                    'instruction' => $val
                ]);
            }
        }

        return (int)$recipe['id'];
    }
}