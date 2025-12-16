<?php

namespace App\Tests\Application\Address\Query;

use App\Application\Address\Query\FindAddressByAddressDeactivatedQuery;
use App\Application\Address\Query\FindAddressByAddressDeactivatedQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\Address\ValueObject\Point;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Address\AddressRepository;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class FindAddressByAddressDeactivatedQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindAddressByAddressDeactivatedQuery('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindAddressByAddressDeactivatedQueryHandler::class)
        );
    }

    public function testFindAddressByAddressQueryHandler()
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
        $city = CityFixture::getOne($region);
        $repositoryCity->create($city);
        $city = $repositoryCity->ofId($city->getId());

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);
        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne(), isActive: false);
        $repositoryAddress->create($address);

        $findAddress = $container->get(FindAddressByAddressDeactivatedQueryHandler::class)(
            new FindAddressByAddressDeactivatedQuery('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1')
        );

        $this->assertNotNull($findAddress);
        $this->assertInstanceOf(Address::class, $findAddress);
        $this->assertEquals($address->getId(), $findAddress->getId());
        $this->assertEquals($address->getStreet(), $findAddress->getStreet());
        $this->assertEquals($address->getFlat(), $findAddress->getFlat());
        $this->assertEquals($address->getPostalCode(), $findAddress->getPostalCode());
        $this->assertEquals($address->getHouse(), $findAddress->getHouse());
        $this->assertEquals($address->getEntrance(), $findAddress->getEntrance());
        $this->assertEquals($address->getFloor(), $findAddress->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertFalse($findAddress->isActive());
        $this->assertNotNull($findAddress->getCreatedAt());
        $this->assertNotNull($findAddress->getUpdatedAt());
    }
}