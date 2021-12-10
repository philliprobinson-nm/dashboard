<?php

namespace App\Domain\Service\Investments\Symbols;

use Laminas\Config\Config;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use Michelf\Markdown;

final class GetStockOverview {
    private $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }
    
    public function getStockOverview(array $args): string {
        $validator = v::key('symbol', v::stringType()->length(1, 8))
            ->key('tickerType', v::stringType()->oneOf(v::equals('Crypto'), v::equals('Stock')));

        try {
            $validator->assert($args);
        } catch(NestedValidationException $e) {
            return \json_encode(['status'=>'fail', 'message'=> Markdown::defaultTransform($e->getFullMessage())]);
        }

        if ($args['tickerType'] == "Crypto") {
            $overview = \file_get_contents('https://pro-api.coinmarketcap.com/v1/cryptocurrency/info?symbol='.$args['symbol'].'&CMC_PRO_API_KEY='.$this->config->get('coinmarketcap')->apiKey);
            $overview = \json_decode($overview);
            
            $data = [
                'status'=>'success', 
                'data'=>reset($overview->data)
            ];
        } elseif ($args['tickerType'] == "Stock") {
            $overview = \file_get_contents('https://www.alphavantage.co/query?function=OVERVIEW&symbol='.$args['symbol'].'&apikey='.$this->config->get('alphavantage')->apiKey);
            $overview = \json_decode($overview);
            
            $data = [
                'status'=>'success', 
                'data'=>$overview
            ];
        }

        return \json_encode($data);
    }
}