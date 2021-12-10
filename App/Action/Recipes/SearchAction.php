<?php

namespace App\Action\Recipes;

use Slim\Views\Twig;
use App\Domain\Service\Recipes\Search;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SearchAction {
    private $twig;
    private $search;

    public function __construct(Twig $twig, Search $search) {
        $this->twig = $twig;
        $this->search = $search;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $term = $request->getQueryParams()['term'];

        $result = $this->search->search($term);
        
        $response->getBody()->write(json_encode($result));

        return $response->withHeader('Content-Type', 'application/json');
    }
}