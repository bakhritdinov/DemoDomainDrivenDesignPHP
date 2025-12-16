<?php

namespace App\Tests\Core\Domain\Currency\Entity;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Entity\CurrencyRate;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Currency\CurrencyRateFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyRateTest extends KernelTestCase
{
    public function testCreateCurrencyRate()
    {
        $currencyRub = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyByn = CurrencyFixture::getOne('BYN', 933, 'Belarusian ruble');

        $currencyRate = CurrencyRateFixture::getOne($currencyRub, $currencyByn, 0.04);

        $this->assertNotNull($currencyRate);
        $this->assertInstanceOf(CurrencyRate::class, $currencyRate);
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyFrom());
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyTo());
        $this->assertEquals(0.04, $currencyRate->getRate());
        $this->assertNotNull($currencyRate->getCreatedAt());
        $this->assertNull($currencyRate->getExpiredAt());
    }

    public function testUpdateCurrencyRate()
    {
        $currencyRub = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyByn = CurrencyFixture::getOne('BYN', 933, 'Belarusian ruble');

        $currencyRate = CurrencyRateFixture::getOne($currencyRub, $currencyByn, 0.04);

        $this->assertNotNull($currencyRate);
        $this->assertInstanceOf(CurrencyRate::class, $currencyRate);
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyFrom());
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyTo());
        $this->assertEquals(0.04, $currencyRate->getRate());
        $this->assertNotNull($currencyRate->getCreatedAt());
        $this->assertNull($currencyRate->getExpiredAt());

        $currencyRate->expired();

        $this->assertNotNull($currencyRate->getExpiredAt());
    }
}