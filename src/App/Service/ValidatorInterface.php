<?php
declare(strict_types=1);

namespace App\Service;

interface ValidatorInterface
{
    public function validate($value): bool;
}
