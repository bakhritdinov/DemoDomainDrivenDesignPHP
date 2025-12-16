<?php

namespace App\Tests\Fixture\Currency;

use App\Core\Domain\Currency\Dto\CreateCurrencyRateDto;

class CreateCurrencyRateDtoFixture
{
    public static function getOne(
        string $currencyCodeFrom,
        string $currencyCodeTo,
        float  $rate
    ): CreateCurrencyRateDto
    {
        return new CreateCurrencyRateDto(
            $currencyCodeFrom, $currencyCodeTo, $rate
        );
    }
}