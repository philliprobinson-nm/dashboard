<?php

namespace App\Action\Investments\Symbols;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Service\Investments\Symbols\FindAll;

final class FindAllAction {
    private $twig;
    private $findAll;

    public function __construct(Twig $twig, FindAll $findAll) {
        $this->twig = $twig;
        $this->findAll = $findAll;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $args = $request->getQueryParams();


        $response->getBody()->write(json_encode($this->findAll->findAll($args)));

        return $response->withHeader('Content-Type', 'application/json');
    }
}