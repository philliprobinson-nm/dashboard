<?php

namespace App\Domain\Service\Recipes;

use App\Domain\Repository\Recipes\ListSingleRepository;

final class RecipesListSingle {
    private $listSingleRepository;

    public function __construct(ListSingleRepository $listSingleRepository) {
        $this->listSingleRepository = $listSingleRepository;
    }
    
    public function RecipesListSingle(int $id): array {
        return $this->listSingleRepository->listSingle($id);
    }
}