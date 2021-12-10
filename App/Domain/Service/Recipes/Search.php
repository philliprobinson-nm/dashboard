<?php

namespace App\Domain\Service\Recipes;

use App\Domain\Repository\Recipes\SearchRepository;

final class Search {
    private $searchRepository;

    public function __construct(SearchRepository $searchRepository) {
        $this->searchRepository = $searchRepository;
    }
    
    public function search(string $term): array {
        $titles = $this->searchRepository->search($term);

        return $titles;
    }
}