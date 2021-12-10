<?php

use Slim\App;

return function (App $app) {
    $app->get('/', \App\Action\IndexAction::class)->setName('index');

    $app->get('/investments/overview', \App\Action\Investments\OverviewAction::class)->setName('investments.overview');
    $app->get('/investments/settings', \App\Action\Investments\SettingsAction::class)->setName('investments.settings');
    $app->get('/investments/symbols/list', \App\Action\Investments\Symbols\FindAllAction::class)->setName('investments.symbols.list');
    $app->post('/investments/symbols/create', \App\Action\Investments\Symbols\CreateAction::class)->setName('investments.symbols.create');
    $app->get('/investments/stock/overview', \App\Action\Investments\Symbols\GetStockOverviewAction::class)->setName('investments.stock.overview');
    
    $app->get('/cron/crypto', \App\Action\Investments\Cron\CryptoAction::class)->setName('cron.crypto');

    $app->get('/recipes/search', \App\Action\Recipes\SearchAction::class)->setName('recipes.search');
    $app->get('/recipes/list[/{page:[0-9]+}]', \App\Action\Recipes\ListAction::class)->setName('recipes.list');
    $app->get('/recipes/{id:[0-9]+}', \App\Action\Recipes\ListSingleAction::class)->setName('recipes.list.single');
    $app->get('/recipes/import', \App\Action\Recipes\ImportAction::class)->setName('recipes.import');
    $app->post('/recipes/import', \App\Action\Recipes\ImportFormAction::class)->setName('recipes.import.form');
};