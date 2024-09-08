<?php
declare(strict_types=1);

namespace Service;

use App\Service\Currencies;
use App\Service\CurrencyFilter;
use App\Service\NbpClient;
use App\Service\ValidatorInterface;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class NbpClientTest extends TestCase
{
    /** @var ValidatorInterface|MockBuilder */
    private $validator;

    /** @var HttpClientInterface|MockBuilder */
    private $httpClient;

    public function setUp(): void
    {
        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $this->validator
            ->method('validate')
            ->willReturn(true);

        $this->httpClient = $this->getMockBuilder(HttpClientInterface::class)->getMock();

    }

    /** @test */
    public function getRates()
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response
            ->method('toArray')
            ->willReturn([
                [
                    'rates' => [
                        [
                            'code' => 'EUR',
                            'currency' => 'Euro',
                            'mid' => 4.55
                        ],
                    ],
                ],
                [
                    'rates' => [
                        [
                            'code' => 'TST',
                            'currency' => 'Test',
                            'mid' => 0.02
                        ],
                    ],
                ],
            ]);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $client = new NbpClient(
            $this->httpClient,
            $this->validator,
            new CurrencyFilter(new Currencies())
        );

        $expected = [
            [
                "currency" => "Euro",
                "code" => "EUR",
                "mid" => 4.55,
                "sell" => 4.62,
                "buy" => 4.5,
                "today_mid" => 4.55,
                "today_sell" => 4.62,
                "today_buy" => 4.5,
            ]
        ];

        $this->assertSame($expected, $client->getRates('2024-09-06'));
    }
}
