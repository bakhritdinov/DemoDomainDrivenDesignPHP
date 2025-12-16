<?php

namespace App\Tests\Application\Currency\Query;

use App\Application\Currency\Query\FindCurrencyByNumDeactivatedQuery;
use App\Application\Currency\Query\FindCurrencyByNumDeactivatedQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\MessageBusTestCase;

class FindCurrencyByNumDeactivatedQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCurrencyByNumDeactivatedQuery(810)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCurrencyByNumDeactivatedQueryHandler::class)
        );
    }

    public function testFindCurrencyByNumQueryHandler()
    {
        $newCurrency = CurrencyFixture::getOneDeactivated('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $currency = $container->get(FindCurrencyByNumDeactivatedQueryHandler::class)(
            new FindCurrencyByNumDeactivatedQuery($newCurrency->getNum())
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