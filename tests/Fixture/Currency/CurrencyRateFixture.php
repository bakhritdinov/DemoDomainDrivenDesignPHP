<?php

namespace App\Tests\Fixture\Currency;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Entity\CurrencyRate;
use Symfony\Component\Uid\Uuid;

class CurrencyRateFixture
{
    public static function getOne(Currency $currencyFrom, Currency $currencyTo, float  $rate, ?Uuid $id = null): CurrencyRate
    {
        $currencyRate = new CurrencyRate($currencyFrom, $currencyTo, $rate);

        $reflectionClass = new \ReflectionClass(CurrencyRate::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($currencyRate, $id ?: Uuid::v1());

        return $currencyRate;
    }
}