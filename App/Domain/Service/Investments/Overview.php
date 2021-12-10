<?php

namespace App\Domain\Service\Investments\Symbols;

use App\Domain\Repository\Investments\OverviewRepository;

final class Overview {
    private $overviewRepository;

    public function __construct(OverviewRepository $overviewRepository) {
        $this->overviewRepository = $overviewRepository;
    }
    
    public function overview(array $args=[]): array {
        $args['offset'] = ($args['page'] - 1) * $args['size'];

        $symbols = $this->overviewRepository->overview($args);

        return $symbols;
    }
}