<?php

namespace App\Tests\Infrastructure\Repository\Currency;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Entity\CurrencyRate;
use App\Core\Domain\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Currency\CurrencyRateFixture;

class CurrencyRateRepositoryTest extends DoctrineTestCase
{
    public function testCreateCurrencyRate()
    {
        $container = $this->getContainer();

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRateRepository = $container->get(CurrencyRateRepositoryInterface::class);

        $currencyRub = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyRepository->create($currencyRub);
        $currencyByn = CurrencyFixture::getOne('BYN', 933, 'Russian ruble');
        $currencyRepository->create($currencyByn);


        $currencyFrom = $currencyRepository->ofCode('RUB');
        $currencyTo = $currencyRepository->ofCode('BYN');
        $currencyRate = CurrencyRateFixture::getOne($currencyFrom, $currencyTo, 0.04);

        $currencyRateRepository->create($currencyRate);

        $currencyRate = $currencyRateRepository->ofCurrencyFromAndCurrencyTo($currencyFrom, $currencyTo);

        $this->assertNotNull($currencyRate);
        $this->assertInstanceOf(CurrencyRate::class, $currencyRate);
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyFrom());
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyTo());
        $this->assertEquals(0.04, $currencyRate->getRate());
        $this->assertNotNull($currencyRate->getCreatedAt());
        $this->assertNull($currencyRate->getExpiredAt());
    }

    public function testOfCurrencyFrom()
    {
        $container = $this->getContainer();

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRateRepository = $container->get(CurrencyRateRepositoryInterface::class);

        $currencyRub = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyRepository->create($currencyRub);
        $currencyByn = CurrencyFixture::getOne('BYN', 933, 'Russian ruble');
        $currencyRepository->create($currencyByn);


        $currencyFrom = $currencyRepository->ofCode('RUB');
        $currencyTo = $currencyRepository->ofCode('BYN');
        $currencyRate = CurrencyRateFixture::getOne($currencyFrom, $currencyTo, 0.04);

        $currencyRateRepository->create($currencyRate);

        $currencyRates = $currencyRateRepository->ofCurrencyFrom($currencyFrom);

        $currencyRate = reset($currencyRates);
        $this->assertNotNull($currencyRate);
        $this->assertInstanceOf(CurrencyRate::class, $currencyRate);
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyFrom());
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyTo());
        $this->assertEquals(0.04, $currencyRate->getRate());
        $this->assertNotNull($currencyRate->getCreatedAt());
        $this->assertNull($currencyRate->getExpiredAt());
    }

    public function testOfCurrencyFromAndCurrencyTo()
    {
        $container = $this->getContainer();

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRateRepository = $container->get(CurrencyRateRepositoryInterface::class);

        $currencyRub = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyRepository->create($currencyRub);
        $currencyByn = CurrencyFixture::getOne('BYN', 933, 'Russian ruble');
        $currencyRepository->create($currencyByn);


        $currencyFrom = $currencyRepository->ofCode('RUB');
        $currencyTo = $currencyRepository->ofCode('BYN');
        $currencyRate = CurrencyRateFixture::getOne($currencyFrom, $currencyTo, 0.04);

        $currencyRateRepository->create($currencyRate);

        $currencyRate = $currencyRateRepository->ofCurrencyFromAndCurrencyTo($currencyFrom, $currencyTo);

        $this->assertNotNull($currencyRate);
        $this->assertInstanceOf(CurrencyRate::class, $currencyRate);
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyFrom());
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyTo());
        $this->assertEquals(0.04, $currencyRate->getRate());
        $this->assertNotNull($currencyRate->getCreatedAt());
        $this->assertNull($currencyRate->getExpiredAt());
    }

}