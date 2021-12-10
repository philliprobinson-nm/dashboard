<?php

namespace App\Action\Recipes;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use App\Domain\Service\Recipes\RecipesList;

final class ListAction {
    private $twig;

    private $recipesCount;
    private $recipesList;

    public function __construct(Twig $twig, RecipesList $recipesList) {
        $this->twig = $twig;
        $this->recipesList = $recipesList;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        if (!isset($args['page']) || !is_numeric($args['page'])) {
            $args['page'] = 1;
        }

        $data = $this->recipesList->recipesList($args);

        return $this->twig->render($response, 'recipes/list.html.twig', $data);
    }
}