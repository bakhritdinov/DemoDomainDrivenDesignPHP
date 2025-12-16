<?php

namespace App\Tests\Application\Currency\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Currency\Command\UpdateCurrencyCommand;
use App\Application\Currency\Command\UpdateCurrencyCommandHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Currency\UpdateCurrencyDtoFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCurrencyCommandTest extends MessageBusTestCase
{

    public function testCommandInstanceOf()
    {
        $updateCurrencyDto = UpdateCurrencyDtoFixture::getOne(null, null, null, false);

        $this->assertInstanceOf(
            Command::class,
            new UpdateCurrencyCommand(Uuid::v1(), $updateCurrencyDto)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateCurrencyCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $updateCurrencyDto = UpdateCurrencyDtoFixture::getOne(null, null, null, false);

        $command = new UpdateCurrencyCommand(Uuid::v1(), $updateCurrencyDto);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateCurrencyCommandHandler()
    {
        $container = $this->getContainer();
        $currencyRepository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $currencyRepository);
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $currencyRepository->create($newCurrency);

        $updateCurrencyDto = UpdateCurrencyDtoFixture::getOne(null, null, null, false);

        $container->get(UpdateCurrencyCommandHandler::class)(
            new UpdateCurrencyCommand($newCurrency->getId(), $updateCurrencyDto)
        );

        $currency = $container->get(CurrencyRepositoryInterface::class)->ofCodeDeactivated($newCurrency->getCode());

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