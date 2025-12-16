<?php

namespace App\Tests\Application\Region\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Region\Command\UpdateRegionCommand;
use App\Application\Region\Command\UpdateRegionCommandHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Region\UpdateRegionDtoFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateRegionCommandTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateRegionCommand(Uuid::v1(), UpdateRegionDtoFixture::getOne())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateRegionCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateRegionCommand(Uuid::v1(), UpdateRegionDtoFixture::getOne());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateRegionCommandHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $newRegion = RegionFixture::getOne($country);
        $repositoryRegion->create($newRegion);

        $container->get(UpdateRegionCommandHandler::class)(
            new UpdateRegionCommand($newRegion->getId(), UpdateRegionDtoFixture::getOne(name: 'Moscow2'))
        );

        $region = $container->get(RegionRepositoryInterface::class)->ofCode('RU-MOW');

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow2', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
    }
}