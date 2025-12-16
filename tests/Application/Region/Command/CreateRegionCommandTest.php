<?php

namespace App\Tests\Application\Region\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Region\Command\CreateRegionCommand;
use App\Application\Region\Command\CreateRegionCommandHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\CreateRegionDtoFixture;
use App\Tests\MessageBusTestCase;

class CreateRegionCommandTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateRegionCommand(CreateRegionDtoFixture::getOne())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateRegionCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateRegionCommand(CreateRegionDtoFixture::getOne());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateRegionCommandHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $container->get(CreateRegionCommandHandler::class)(
            new CreateRegionCommand(CreateRegionDtoFixture::getOne())
        );

        $region = $container->get(RegionRepositoryInterface::class)->ofCode('RU-MOW');

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
    }
}