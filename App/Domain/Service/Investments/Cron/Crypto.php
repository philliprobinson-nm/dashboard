<?php

namespace App\Domain\Service\Investments\Cron;

use Laminas\Config\Config;
use App\Domain\Repository\Investments\Cron\CryptoRepository;

final class Crypto {
    private $cryptoRepository;

    public function __construct(Config $config, CryptoRepository $cryptoRepository) {
        $this->config = $config;
        $this->cryptoRepository = $cryptoRepository;
    }
    
    public function getLatestQuotes(): void {
        $symbols = $this->cryptoRepository->getSymbols();

        $overview = \file_get_contents('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol='.implode(',', $symbols).'&CMC_PRO_API_KEY='.$this->config->get('coinmarketcap')->apiKey);
        $overview = \json_decode($overview);

        foreach ($symbols as $symbol) {
            $data['investment_symbols_id'] = $this->cryptoRepository->getSymbolId($symbol);
            $data['price'] = $overview->data->$symbol->quote->USD->price;
            $data['volume_24h'] = $overview->data->$symbol->quote->USD->volume_24h;
            $data['percent_change_1h'] = $overview->data->$symbol->quote->USD->percent_change_1h;
            $data['percent_change_24h'] = $overview->data->$symbol->quote->USD->percent_change_24h;
            $data['percent_change_7d'] = $overview->data->$symbol->quote->USD->percent_change_7d;
            $data['percent_change_30d'] = $overview->data->$symbol->quote->USD->percent_change_30d;
            $data['market_cap'] = $overview->data->$symbol->quote->USD->market_cap;

            $this->cryptoRepository->insert($data);
        }
    }
}