<?php

namespace App\Tests\Application\Currency\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Currency\Command\CreateCurrencyRateCommand;
use App\Application\Currency\Command\CreateCurrencyRateCommandHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Entity\CurrencyRate;
use App\Core\Domain\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Application\MessengerCommandBusTest;
use App\Tests\Fixture\Currency\CreateCurrencyRateDtoFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;

class CreateCurrencyRateCommandTest extends MessengerCommandBusTest
{
    public function testCommandInstanceOf()
    {
        $createCurrencyRateDto = CreateCurrencyRateDtoFixture::getOne('RUB', 'BYN', 0.04);

        $this->assertInstanceOf(
            Command::class,
            new CreateCurrencyRateCommand($createCurrencyRateDto)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCurrencyRateCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $createCurrencyRateDto = CreateCurrencyRateDtoFixture::getOne('RUB', 'BYN', 0.04);
        $command = new CreateCurrencyRateCommand($createCurrencyRateDto);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCurrencyRateCommandHandler()
    {
        $container = $this->getContainer();

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRateRepository = $container->get(CurrencyRateRepositoryInterface::class);

        $currencyRub = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyRepository->create($currencyRub);
        $currencyByn = CurrencyFixture::getOne('BYN', 933, 'Russian ruble');
        $currencyRepository->create($currencyByn);

        $createCurrencyRateDto = CreateCurrencyRateDtoFixture::getOne('RUB', 'BYN', 0.04);

        $container->get(CreateCurrencyRateCommandHandler::class)(
            new CreateCurrencyRateCommand($createCurrencyRateDto)
        );

        $currencyFrom = $currencyRepository->ofCode('RUB');
        $currencyTo = $currencyRepository->ofCode('BYN');

        $this->assertNotNull($currencyFrom);
        $this->assertInstanceOf(Currency::class, $currencyFrom);
        $this->assertEquals('RUB', $currencyFrom->getCode());
        $this->assertEquals(810, $currencyFrom->getNum());
        $this->assertEquals('Russian ruble', $currencyFrom->getName());
        $this->assertInstanceOf(CurrencyRate::class, $currencyFrom->getCurrencyRates()->first());
        $this->assertInstanceOf(CurrencyRate::class, $currencyFrom->getCurrencyRateByCurrencyToCode($currencyTo));
        $this->assertEquals(0.04, $currencyFrom->getCurrencyRates()->first()->getRate());
        $this->assertTrue($currencyFrom->isActive());
        $this->assertNotNull($currencyFrom->getCreatedAt());
        $this->assertNull($currencyFrom->getUpdatedAt());


        $currencyRate = $currencyRateRepository->ofCurrencyFromAndCurrencyTo($currencyFrom, $currencyTo);

        $this->assertNotNull($currencyRate);
        $this->assertInstanceOf(CurrencyRate::class, $currencyRate);
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyFrom());
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyTo());
        $this->assertEquals(0.04, $currencyRate->getRate());
        $this->assertNotNull($currencyRate->getCreatedAt());
        $this->assertNull($currencyRate->getExpiredAt());

    }

    public function testCreateMultipleCurrencyRateCommandHandler()
    {
        $container = $this->getContainer();

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRateRepository = $container->get(CurrencyRateRepositoryInterface::class);

        $currencyRub = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyRepository->create($currencyRub);
        $currencyByn = CurrencyFixture::getOne('BYN', 933, 'Belarusian ruble');
        $currencyRepository->create($currencyByn);

        $createCurrencyRateDto = CreateCurrencyRateDtoFixture::getOne('RUB', 'BYN', 0.04);

        $container->get(CreateCurrencyRateCommandHandler::class)(
            new CreateCurrencyRateCommand($createCurrencyRateDto)
        );

        $createCurrencyRateDto = CreateCurrencyRateDtoFixture::getOne('RUB', 'BYN', 0.03);

        $container->get(CreateCurrencyRateCommandHandler::class)(
            new CreateCurrencyRateCommand($createCurrencyRateDto)
        );

        $currencyFrom = $currencyRepository->ofCode('RUB');
        $currencyTo = $currencyRepository->ofCode('BYN');

        $this->assertNotNull($currencyFrom);
        $this->assertInstanceOf(Currency::class, $currencyFrom);
        $this->assertEquals('RUB', $currencyFrom->getCode());
        $this->assertEquals(810, $currencyFrom->getNum());
        $this->assertEquals('Russian ruble', $currencyFrom->getName());
        $this->assertInstanceOf(CurrencyRate::class, $currencyFrom->getCurrencyRateByCurrencyToCode($currencyTo));
        $this->assertEquals(0.03, $currencyFrom->getCurrencyRateByCurrencyToCode($currencyTo)->getRate());
        $this->assertTrue($currencyFrom->isActive());
        $this->assertNotNull($currencyFrom->getCreatedAt());
        $this->assertNull($currencyFrom->getUpdatedAt());


        $currencyRate = $currencyRateRepository->ofCurrencyFromAndCurrencyTo($currencyFrom, $currencyTo);

        $this->assertNotNull($currencyRate);
        $this->assertInstanceOf(CurrencyRate::class, $currencyRate);
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyFrom());
        $this->assertInstanceOf(Currency::class, $currencyRate->getCurrencyTo());
        $this->assertEquals(0.03, $currencyRate->getRate());
        $this->assertNotNull($currencyRate->getCreatedAt());
        $this->assertNull($currencyRate->getExpiredAt());

    }
}