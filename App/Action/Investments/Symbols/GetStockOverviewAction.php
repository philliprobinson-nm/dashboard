<?php

namespace App\Action\Investments\Symbols;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Service\Investments\Symbols\GetStockOverview;

final class GetStockOverviewAction {
    private $twig;

    public function __construct(Twig $twig, GetStockOverview $getStockOverview) {
        $this->twig = $twig;
        $this->getStockOverview = $getStockOverview;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $args = $request->getQueryParams();
        $response->getBody()->write($this->getStockOverview->getStockOverview($args));

        return $response->withHeader('Content-Type', 'application/json');
    }
}