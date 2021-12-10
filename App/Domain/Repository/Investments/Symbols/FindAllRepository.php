<?php

namespace App\Domain\Repository\Investments\Symbols;

use MeekroDB;

final class FindAllRepository {
    private $db;

    public function __construct(MeekroDB $db) {
        $this->db = $db;
    }

    public function findAll(array $args): array {
        $total = $this->db->queryFirstField("select count(id) from investment_symbols");

        $orderBy = "";
        if (isset($args['sorters'])) {
            $orderBy = "order by";
            foreach ($args['sorters'] as $sorter) {
                $orderBy .= " ".$sorter['field']." ".$sorter['dir'];
            }
        }

        $ret['last_page'] = ceil($total / $args['size']);
        $ret['data'] = $this->db->query("select * from investment_symbols $orderBy limit %i offset %i", $args['size'], $args['offset']);

        return $ret;
    }
}