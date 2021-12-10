<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class ArchiveEmealsRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function archiveEmeals($recipe): void {
        $this->db->insert('recipe_urls', [
            'title'=>$recipe['title'], 
            'planNum'=>$recipe['planId'], 
            'recipeNum'=>$recipe['recipeId'],
            'result'=>$recipe['result']
        ]);

        if ($recipe['result'] == 200) {
            $this->db->insert('recipe_body', [
                'id'=> $this->db->insertId(), 
                'body'=>bin2hex(gzcompress(gzcompress($recipe['body'], 9), 9))
            ]);
        }
    }
}