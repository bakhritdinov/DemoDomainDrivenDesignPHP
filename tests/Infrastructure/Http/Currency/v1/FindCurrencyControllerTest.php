<?php

namespace App\Tests\Infrastructure\Http\Currency\v1;

use App\Core\Domain\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Currency\CurrencyRateFixture;
use App\Tests\HttpTestCase;

class FindCurrencyControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $container = $this->getContainer();

        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);
        $currencyRateRepository = $container->get(CurrencyRateRepositoryInterface::class);

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency2 = CurrencyFixture::getOne('KZ', 398, 'Kazakhstani tenge');
        $newCurrency3 = CurrencyFixture::getOne('BYN', 988, 'Belarusian ruble');
        $repository->create($newCurrency);
        $repository->create($newCurrency2);
        $repository->create($newCurrency3);

        $currencyFrom = $repository->ofCode($newCurrency->getCode());
        $currencyTo = $repository->ofCode($newCurrency2->getCode());
        $currencyRate = CurrencyRateFixture::getOne($currencyFrom, $currencyTo, 0.04);
        $currencyRateRepository->create($currencyRate);

        $currencyRate = $currencyRateRepository->ofCurrencyFromAndCurrencyTo($currencyFrom, $currencyTo);

        $currencyRate->expired();
        $currencyRateRepository->update($currencyRate);

        $currencyFrom = $repository->ofCode($newCurrency->getCode());
        $currencyTo = $repository->ofCode($newCurrency2->getCode());
        $currencyRate = CurrencyRateFixture::getOne($currencyFrom, $currencyTo, 0.12);
        $currencyRateRepository->create($currencyRate);

        $currencyFrom = $repository->ofCode($newCurrency->getCode());
        $currencyTo1 = $repository->ofCode($newCurrency3->getCode());
        $currencyRate1 = CurrencyRateFixture::getOne($currencyFrom, $currencyTo1, 0.03);

        $currencyRateRepository->create($currencyRate1);

        $this->client
            ->request(
                'GET',
                "/api/v1/currency/find-by-id/{$newCurrency->getId()->toRfc4122()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('rates', $response);
        $this->assertStringContainsString('rate', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }

    public function testFindByCode()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client
            ->request(
                'GET',
                "/api/v1/currency/find-by-code/{$newCurrency->getCode()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }

    public function testFindByNum()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client
            ->request(
                'GET',
                "/api/v1/currency/find-by-num/{$newCurrency->getNum()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }

    public function testFindByIdDeactivated()
    {
        $newCurrency = CurrencyFixture::getOneDeactivated('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client
            ->request(
                'GET',
                "/api/v1/currency/find-by-id/{$newCurrency->getId()->toRfc4122()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }

    public function testFindByCodeDeactivated()
    {
        $newCurrency = CurrencyFixture::getOneDeactivated('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client
            ->request(
                'GET',
                "/api/v1/currency/find-by-code/{$newCurrency->getCode()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }

    public function testFindByNumDeactivated()
    {
        $newCurrency = CurrencyFixture::getOneDeactivated('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client
            ->request(
                'GET',
                "/api/v1/currency/find-by-num/{$newCurrency->getNum()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }
}