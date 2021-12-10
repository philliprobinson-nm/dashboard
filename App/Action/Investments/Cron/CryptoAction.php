<?php

namespace App\Action\Investments\Cron;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Domain\Service\Investments\Cron\Crypto;

final class CryptoAction {
    private $twig;

    public function __construct(Twig $twig, Crypto $crypto) {
        $this->twig = $twig;
        $this->crypto = $crypto;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $this->crypto->getLatestQuotes();

        $response->getBody()->write(json_encode([]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}