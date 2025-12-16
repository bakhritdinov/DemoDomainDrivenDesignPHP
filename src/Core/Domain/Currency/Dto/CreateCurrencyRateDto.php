<?php

namespace App\Core\Domain\Currency\Dto;

final readonly class CreateCurrencyRateDto
{
    public function __construct(
        public string $currencyCodeFrom,
        public string $currencyCodeTo,
        public float  $rate
    )
    {
    }
}