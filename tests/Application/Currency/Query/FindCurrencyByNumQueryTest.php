<?php

namespace App\Tests\Application\Currency\Query;

use App\Application\Currency\Query\FindCurrencyByNumQuery;
use App\Application\Currency\Query\FindCurrencyByNumQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\MessageBusTestCase;

class FindCurrencyByNumQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCurrencyByNumQuery(810)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCurrencyByNumQueryHandler::class)
        );
    }

    public function testFindCurrencyByNumQueryHandler()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $currency = $container->get(FindCurrencyByNumQueryHandler::class)(
            new FindCurrencyByNumQuery($newCurrency->getNum())
        );

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }
}