<?php
declare(strict_types=1);

namespace Service;

use App\Service\DateValidator;
use PHPUnit\Framework\TestCase;

class DateValidatorTest extends TestCase
{
    /**
     * @dataProvider validDatesDataProvider
     * @test
     */
    public function valid_dates($date)
    {
        $validator = new DateValidator();
        $this->assertTrue($validator->validate($date));
    }

    /**
     * @dataProvider inValidDatesDataProvider
     * @test
     */
    public function invalid_dates($date, $expectedError)
    {
        $this->expectExceptionMessage($expectedError);

        $validator = new DateValidator();
        $validator->validate($date);
    }

    public function validDatesDataProvider(): array
    {
        return [
            ['2024-01-10'],
            ['2024-05-10'],
            ['2024-08-12'],
        ];
    }

    public function inValidDatesDataProvider(): array
    {
        return [
            ['2022-08-12', 'Date cannot be earlier then \'2023-01-01\''],
            ['2023-08-6', 'Date format is not valid'],
            ['2024-09-01', 'Weekends are not allowed'],
            ['2048-08-10', 'Date cannot be in the future'],
        ];
    }
}
