<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpClient
{
    private const ENDPOINT = 'https://api.nbp.pl/api/exchangerates/tables/A/%s?format=json';
    private const POPULAR_CURRENCIES = ['USD', 'EUR'];
    private const CURRENCY_SELL_SPREAD = 0.15;
    private const POPULAR_CURRENCY_BUY_SPREAD = 0.05;
    private const POPULAR_CURRENCY_SELL_SPREAD = 0.07;

    /** @var HttpClientInterface */
    private $client;

    /** @var DateValidator */
    private $dateValidator;

    /** @var FilterInterface */
    private $currencyFilter;

    public function __construct(HttpClientInterface $client, ValidatorInterface $dateValidator, FilterInterface $currencyFilter)
    {
        $this->client = $client;
        $this->dateValidator = $dateValidator;
        $this->currencyFilter = $currencyFilter;
    }

    public function getRates(string $date): array
    {
        $this->dateValidator->validate($date);

        $responseDate = $this->getRatesForDate($date);
        $responseToday = $this->getRatesForToday($date, $responseDate);

        return $this->prepareData($responseDate, $responseToday);
    }

    private function getRatesForDate(string $date = ''): array
    {
        return $this->currencyFilter->filter($this->client->request('GET', sprintf(self::ENDPOINT, $date))->toArray());
    }

    private function getRatesForToday(string $date, array $response): array
    {
        if ($date === date('Y-m-d')) {
            return $response;
        }
        return $this->getRatesForDate();
    }

    private function prepareData(array $response, array $responseToday): array
    {
        return array_map(function ($rate) use ($responseToday) {
            $filtered = array_filter($responseToday, function ($item) use ($rate) {
                return $item['code'] === $rate['code'];
            });

            $rateToday = current($filtered);

            return ['currency' => $rate['currency'], 'code' => $rate['code'], 'mid' => $rate['mid'], 'sell' => $this->getSellRate($rate), 'buy' => $this->getBuyRate($rate), 'today_mid' => $rateToday['mid'], 'today_sell' => $this->getSellRate($rateToday), 'today_buy' => $this->getBuyRate($rateToday),];
        }, $response);
    }

    private function getSellRate(array $rate): ?float
    {
        if (in_array($rate['code'], self::POPULAR_CURRENCIES, true)) {
            return $rate['mid'] + self::POPULAR_CURRENCY_SELL_SPREAD;
        }
        return $rate['mid'] + self::CURRENCY_SELL_SPREAD;
    }

    private function getBuyRate(array $rate): ?float
    {
        if (in_array($rate['code'], self::POPULAR_CURRENCIES, true)) {
            return $rate['mid'] - self::POPULAR_CURRENCY_BUY_SPREAD;
        }
        return null;
    }
}
