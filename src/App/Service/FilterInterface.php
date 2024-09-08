<?php
declare(strict_types=1);

namespace App\Service;

interface FilterInterface
{
    public function filter(array $data): array;
}
