<?php
declare(strict_types=1);

namespace App\Service;

class CurrencyFilter implements FilterInterface
{
    /** @var CurrenciesInterface $currencies */
    private $currencies;

    public function __construct(CurrenciesInterface $currencies)
    {
        $this->currencies = $currencies;
    }

    public function filter(array $data): array
    {
        return array_values(array_filter($data[0]['rates'], function ($item) {
            return array_key_exists($item['code'], $this->currencies->getCurrencies());
        }));
    }
}
