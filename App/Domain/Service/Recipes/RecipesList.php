<?php

namespace App\Domain\Service\Recipes;

use App\Domain\Repository\Recipes\ListRepository;
use App\Domain\Repository\Recipes\CountRepository;

final class RecipesList {
    private $listRepository;
    private $countRepository;

    public function __construct(ListRepository $listRepository, CountRepository $countRepository) {
        $this->listRepository = $listRepository;
        $this->countRepository = $countRepository;
    }
    
    public function RecipesList(array $args=[]): array {
        $data['limit'] = 12;
        $data['offset'] = ($args['page'] - 1) * $data['limit'];

        $recipes['pages']['current'] = $args['page'];
        $recipes['pages']['total'] = ceil($this->countRepository->count() / $data['limit']);
        $recipes['rows'] = $this->listRepository->list($data);

        return $recipes;
    }
}