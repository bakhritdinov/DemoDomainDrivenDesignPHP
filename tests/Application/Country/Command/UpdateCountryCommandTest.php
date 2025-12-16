<?php

namespace App\Tests\Application\Country\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Country\Command\UpdateCountryCommand;
use App\Application\Country\Command\UpdateCountryCommandHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Country\UpdateCountryDtoFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCountryCommandTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateCountryCommand(Uuid::v1(), UpdateCountryDtoFixture::getOne())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateCountryCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateCountryCommand(Uuid::v1(), UpdateCountryDtoFixture::getOne());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateCountryCommandHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country');
        $repositoryCountry->create($newCountry);

        $container->get(UpdateCountryCommandHandler::class)(
            new UpdateCountryCommand($newCountry->getId(), UpdateCountryDtoFixture::getOne(name:'updated test country', isActive:  true))
        );

        $country = $container->get(CountryRepositoryInterface::class)->ofAlpha2('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('updated test country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNotNull($country->getUpdatedAt());
    }
}