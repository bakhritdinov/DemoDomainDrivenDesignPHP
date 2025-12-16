<?php

namespace App\Tests\Application\Address\Command;

use App\Application\Address\Command\CreateAddressCommand;
use App\Application\Address\Command\CreateAddressCommandHandler;
use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\Address\ValueObject\Point;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Address\AddressRepository;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class CreateAddressCommandTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateAddressCommand('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateAddressCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateAddressCommand('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateAddressCommandHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $region = RegionFixture::getOne($country, code: 'RU-BEL');
        $repositoryRegion->create($region);
        $region = $repositoryRegion->ofId($region->getId());

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);
        $city = CityFixture::getOne($region);
        $repositoryCity->create($city);

        $service = $this->createMock(FindExternalAddressInterface::class);
        $service->method('find')->willReturn(DaDataAddressDtoFixture::getOne());
        $container->set(FindExternalAddressInterface::class, $service);

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $addressString = '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1';
        $container->get(CreateAddressCommandHandler::class)(
            new CreateAddressCommand($addressString)
        );

        $address = $container->get(AddressRepositoryInterface::class)->ofAddress($addressString);

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
    }
}