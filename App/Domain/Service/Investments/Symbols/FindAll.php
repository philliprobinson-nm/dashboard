<?php

namespace App\Domain\Service\Investments\Symbols;

use App\Domain\Repository\Investments\Symbols\FindAllRepository;

final class FindAll {
    private $findAllRepository;

    public function __construct(FindAllRepository $findAllRepository) {
        $this->findAllRepository = $findAllRepository;
    }
    
    public function findAll(array $args=[]): array {
        $args['offset'] = ($args['page'] - 1) * $args['size'];

        $symbols = $this->findAllRepository->findAll($args);

        return $symbols;
    }
}