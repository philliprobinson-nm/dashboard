<?php

namespace App\Action\Recipes;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Service\Recipes\RecipesListSingle;

final class ListSingleAction {
    private $twig;

    private $recipesListSingle;

    public function __construct(Twig $twig, RecipesListSingle $recipesListSingle) {
        $this->twig = $twig;
        $this->recipesListSingle = $recipesListSingle;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        if (!isset($args['id']) || !is_numeric($args['id'])) {
            $args['id'] = 1;
        }

        $data = $this->recipesListSingle->recipesListSingle($args['id']);

        return $this->twig->render($response, 'recipes/list.single.html.twig', $data);
    }
}