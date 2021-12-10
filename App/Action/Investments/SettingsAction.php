<?php

namespace App\Action\Investments;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SettingsAction {
    private $twig;

    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        return $this->twig->render($response, 'investments/settings.html.twig');
    }
}