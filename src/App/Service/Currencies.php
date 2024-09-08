<?php
declare(strict_types=1);

namespace App\Service;

class Currencies implements CurrenciesInterface
{
    private const CURRENCIES = [
        'EUR' => 'euro',
        'USD' => 'dolar amerykański',
        'CZK' => 'korona czeska',
        'IDR' => 'rupia indonezyjska',
        'BRL' => 'real brazylijski',
    ];

    public function getCurrencies(): array
    {
        return self::CURRENCIES;
    }
}
