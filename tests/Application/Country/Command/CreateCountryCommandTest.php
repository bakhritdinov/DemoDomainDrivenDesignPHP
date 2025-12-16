<?php

namespace App\Tests\Application\Country\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Country\Command\CreateCountryCommand;
use App\Application\Country\Command\CreateCountryCommandHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CreateCountryDtoFixture;
use App\Tests\MessageBusTestCase;

class CreateCountryCommandTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateCountryCommand(CreateCountryDtoFixture::getOne())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCountryCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateCountryCommand(CreateCountryDtoFixture::getOne());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCountryCommandHandler()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);

        $container->get(CreateCountryCommandHandler::class)(
            new CreateCountryCommand(CreateCountryDtoFixture::getOne())
        );

        $country = $container->get(CountryRepositoryInterface::class)->ofAlpha2('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('Russia', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }
}