<?php
declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

class DateValidator implements ValidatorInterface
{
    private const START_DATE = '2023-01-01';

    public function validate($value): bool
    {
        if (!preg_match('#^\d{4}-\d{2}-\d{2}$#', $value)) {
            throw new InvalidArgumentException('Date format is not valid');
        }

        try {
            $dateObj = new DateTimeImmutable($value);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Date format is not valid');
        }


        if ($dateObj < new DateTimeImmutable(self::START_DATE)) {
            throw new InvalidArgumentException(sprintf(
                "Date cannot be earlier then '%s'",
                self::START_DATE
            ));
        }

        if ($dateObj > new DateTimeImmutable()) {
            throw new InvalidArgumentException('Date cannot be in the future');
        }

        if ($dateObj->format('N') >= 6) {
            throw new InvalidArgumentException('Weekends are not allowed');
        }

        return true;
    }
}
