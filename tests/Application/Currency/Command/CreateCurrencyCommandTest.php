<?php

namespace App\Tests\Application\Currency\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Currency\Command\CreateCurrencyCommand;
use App\Application\Currency\Command\CreateCurrencyCommandHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\Application\MessengerCommandBusTest;
use App\Tests\Fixture\Currency\CreateCurrencyDtoFixture;

class CreateCurrencyCommandTest extends MessengerCommandBusTest
{

    public function testCommandInstanceOf()
    {
        $createCurrencyDto = CreateCurrencyDtoFixture::getOne('RUB', 810, 'Russian ruble');

        $this->assertInstanceOf(
            Command::class,
            new CreateCurrencyCommand($createCurrencyDto)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCurrencyCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $createCurrencyDto = CreateCurrencyDtoFixture::getOne('RUB', 810, 'Russian ruble');
        $command = new CreateCurrencyCommand($createCurrencyDto);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCurrencyCommandHandler()
    {
        $container = $this->getContainer();

        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);
        $createCurrencyDto = CreateCurrencyDtoFixture::getOne('RUB', 810, 'Russian ruble');

        $container->get(CreateCurrencyCommandHandler::class)(
            new CreateCurrencyCommand($createCurrencyDto)
        );

        $currency = $container->get(CurrencyRepositoryInterface::class)->ofCode('RUB');

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