<?php
declare(strict_types=1);

namespace Service;

use App\Service\CurrenciesInterface;
use App\Service\CurrencyFilter;
use PHPUnit\Framework\TestCase;

class CurrencyFilterTest extends TestCase
{
    /** @test */
    public function filter_data(): void
    {
        $currencies = $this->getMockBuilder(CurrenciesInterface::class)->getMock();

        $currencies
            ->expects($this->once())
            ->method('getCurrencies')
            ->willReturn(['EUR' => 'Euro']);

        $filter = new CurrencyFilter($currencies);

        $results = $filter->filter(
            [
                [
                    'rates' => [
                        ['code' => 'EUR'],
                    ],
                ],
                [
                    'rates' => [
                        ['code' => 'USD'],
                    ],
                ],
            ]
        );

        $this->assertSame([['code' => 'EUR']], $results);
    }
}
