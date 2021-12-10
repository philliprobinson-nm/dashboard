<?php

namespace App\Action\Investments\Symbols;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Service\Investments\Symbols\Create;

final class CreateAction {
    private $twig;
    private $create;

    public function __construct(Twig $twig, Create $create) {
        $this->twig = $twig;
        $this->create = $create;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $args = $request->getParsedBody();

        $response->getBody()->write(json_encode($this->create->create($args)));

        return $response->withHeader('Content-Type', 'application/json');
    }
}