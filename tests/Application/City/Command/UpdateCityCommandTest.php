<?php

namespace App\Tests\Application\City\Command;

use App\Application\City\Command\UpdateCityCommand;
use App\Application\City\Command\UpdateCityCommandHandler;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\City\UpdateCityDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCityCommandTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateCityCommand(Uuid::v1(), UpdateCityDtoFixture::getOne())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateCityCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateCityCommand(Uuid::v1(), UpdateCityDtoFixture::getOne());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateCityCommandHandler()
    {
        $container = $this->getContainer();


        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $region = RegionFixture::getOne($country);
        $repositoryRegion->create($region);
        $region = $repositoryRegion->ofId($region->getId());

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);
        $newCity = CityFixture::getOne($region);
        $repositoryCity->create($newCity);

        $container->get(UpdateCityCommandHandler::class)(
            new UpdateCityCommand($newCity->getId(), UpdateCityDtoFixture::getOne(type: 'village', name: 'Moscow2'))
        );

        $city = $container->get(CityRepositoryInterface::class)->ofRegionAndTypeAndName($region, 'village', 'Moscow2');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moscow2', $city->getName());
        $this->assertEquals('village', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
    }
}