<?php

namespace App\Domain\Repository\Recipes;

use MeekroDB;

final class ListRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function list(array $args): array {
        return (array)$this->db->query("select * from recipes order by id desc limit %i offset %i", $args['limit'], $args['offset']);
    }
}