<?php

namespace App\Action\Recipes;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ImportAction {
    private $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        return $this->twig->render($response, 'recipes/import.html.twig');
    }
}