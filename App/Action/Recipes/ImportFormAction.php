<?php

namespace App\Action\Recipes;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use App\Domain\Service\Recipes\ImportForm;
use Psr\Http\Message\ServerRequestInterface;

final class ImportFormAction {
    private $twig;
    private $importForm;

    public function __construct(Twig $twig, ImportForm $importForm) {
        $this->twig = $twig;
        $this->importForm = $importForm;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $args = (array)$request->getParsedBody();
        $result = $this->importForm->importForm($args['url']);
        
        $response->getBody()->write(json_encode($result));

        return $response->withHeader('Content-Type', 'application/json');
    }
}