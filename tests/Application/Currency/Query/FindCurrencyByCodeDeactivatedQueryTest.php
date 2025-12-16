<?php

namespace App\Tests\Application\Currency\Query;

use App\Application\Currency\Query\FindCurrencyByCodeDeactivatedQuery;
use App\Application\Currency\Query\FindCurrencyByCodeDeactivatedQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\MessageBusTestCase;

class FindCurrencyByCodeDeactivatedQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCurrencyByCodeDeactivatedQuery('RUB')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCurrencyByCodeDeactivatedQueryHandler::class)
        );
    }

    public function testFindCurrencyByCodeDeactivatedQueryHandler()
    {
        $newCurrency = CurrencyFixture::getOneDeactivated('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $currency = $container->get(FindCurrencyByCodeDeactivatedQueryHandler::class)(
            new FindCurrencyByCodeDeactivatedQuery($newCurrency->getCode())
        );

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }
}