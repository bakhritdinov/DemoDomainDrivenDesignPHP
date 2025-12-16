<?php

namespace App\Tests\Infrastructure\Repository\Currency;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Currency\CurrencyFixture;

class CurrencyRepositoryTest extends DoctrineTestCase
{
    public function testCreateCurrency()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');

        $repository->create($newCurrency);

        $currency = $repository->ofId($newCurrency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
    }

    public function testUpdateCurrency()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository->create($newCurrency);

        $this->assertEquals('RUB', $newCurrency->getCode());

        $currency = $repository->ofId($newCurrency->getId());

        $currency->changeName('Updated russian ruble');
        $repository->update($currency);

        $updatedCurrency = $repository->ofId($currency->getId());

        $this->assertNotNull($updatedCurrency->getUpdatedAt());
        $this->assertEquals('Updated russian ruble', $updatedCurrency->getName());

        $this->assertTrue($updatedCurrency->isActive());

        $updatedCurrency->changeIsActive(false);
        $repository->update($updatedCurrency);

        $deactivatedCurrency = $repository->ofIdDeactivated($updatedCurrency->getId());

        $this->assertNotNull($deactivatedCurrency);
        $this->assertFalse($deactivatedCurrency->isActive());
    }

    public function testOfId()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository->create($newCurrency);

        $currency = $repository->ofId($newCurrency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testOfIdDeactivated()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency->changeIsActive(false);
        $repository->create($newCurrency);

        $currency = $repository->ofIdDeactivated($newCurrency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }

    public function testOfCode()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository->create($newCurrency);

        $currency = $repository->ofCode($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testOfCodeDeactivated()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency->changeIsActive(false);
        $repository->create($newCurrency);

        $currency = $repository->ofCodeDeactivated($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }

    public function testOfNum()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository->create($newCurrency);

        $currency = $repository->ofNum($newCurrency->getNum());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testOfNumDeactivated()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency->changeIsActive(false);
        $repository->create($newCurrency);

        $currency = $repository->ofNumDeactivated($newCurrency->getNum());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }

    public function testAll()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency2 = CurrencyFixture::getOne('KZ', 398, 'Kazakhstani tenge');
        $repository->create($newCurrency);
        $repository->create($newCurrency2);

        $currencies = $repository->all();

        $this->assertNotNull($currencies);
        $this->assertIsArray($currencies);

        $currency = reset($currencies);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testPaginate()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency2 = CurrencyFixture::getOne('KZ', 398, 'Kazakhstani tenge');
        $repository->create($newCurrency);
        $repository->create($newCurrency2);

        $currencies = $repository->paginate(1, 2);

        $this->assertNotEmpty($currencies);
        $this->assertIsArray($currencies);
        $this->assertArrayHasKey('data', $currencies);
        $this->assertArrayHasKey('total', $currencies);
        $this->assertArrayHasKey('pages', $currencies);
        $this->assertCount(2, $currencies['data']);
        $this->assertEquals(2, $currencies['total']);
        $this->assertEquals(1, $currencies['pages']);

        $currency = $currencies['data'][0];

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }
}