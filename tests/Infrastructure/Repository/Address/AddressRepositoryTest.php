<?php

namespace App\Tests\Infrastructure\Repository\Address;

use App\Core\Domain\Address\Entity\Address;
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
use App\Tests\DoctrineTestCase;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Component\Uid\Uuid;

class AddressRepositoryTest extends DoctrineTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateAddress()
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
        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $repositoryAddress->create($address);

        $newAddress = $repositoryAddress->ofId($address->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
    }

    public function testUpdateAddress()
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
        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $repositoryAddress->create($address);

        $newAddress = $repositoryAddress->ofId($address->getId());
        $this->assertEquals('309850', $newAddress->getPostalCode());

        $newAddress->changePostalCode('1111111');
        $repositoryAddress->update($newAddress);

        $updatedAddress = $repositoryAddress->ofId($newAddress->getId());

        $this->assertNotNull($updatedAddress->getUpdatedAt());
        $this->assertEquals('1111111', $updatedAddress->getPostalCode());

        $this->assertTrue($updatedAddress->isActive());

        $updatedAddress->changeIsActive(false);
        $repositoryAddress->update($updatedAddress);

        $deactivatedAddress = $repositoryAddress->ofIdDeactivated($updatedAddress->getId());

        $this->assertNotNull($deactivatedAddress);
        $this->assertFalse($deactivatedAddress->isActive());
    }

    public function testOfId()
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
        $newAddress = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $repositoryAddress->create($newAddress);

        $address = $repositoryAddress->ofId($newAddress->getId());

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
    }

    public function testOfAddress()
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
        $newAddress = AddressFixture::getOne($city, '111111, Lenina, 1, 1', '111111', 'Lenina', '1', '1');
        $repositoryAddress->create($newAddress);

        $address = $repositoryAddress->ofAddress('111111, Lenina, 1, 1',);

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
    }

    public function testOfIdDeactivated()
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
        $newAddress = AddressFixture::getOneForIsActive($city, '111111, Lenina, 1, 1', '111111', 'Lenina', '1', '1', null, null, false);
        $repositoryAddress->create($newAddress);

        $address = $repositoryAddress->ofIdDeactivated($newAddress->getId());

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertFalse($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNotNull($address->getUpdatedAt());
    }

    public function testOfAddressDeactivated()
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
        $newAddress = AddressFixture::getOneForIsActive($city, '111111, Lenina, 1, 1', '111111', 'Lenina', '1', '1', null, null, false);
        $repositoryAddress->create($newAddress);

        $address = $repositoryAddress->ofAddressDeactivated('111111, Lenina, 1, 1');

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertFalse($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNotNull($address->getUpdatedAt());
    }

    public function testOfCity()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $city = CityFixture::getOne($region);
        $newAddress = AddressFixture::getOne($city, 'wdwdwd', '1111111', 'ewwe', '1');
        $newAddress2 = AddressFixture::getOne($city, 'dwdwdw', '111111', 'weew', '2');

        $array = [$newAddress, $newAddress2];

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $addresses = $repositoryAddress->ofCityPaginate($city, 1, 2);

        $this->assertNotEmpty($addresses);
        $this->assertIsArray($addresses);
        $this->assertArrayHasKey('data', $addresses);
        $this->assertArrayHasKey('total', $addresses);
        $this->assertArrayHasKey('pages', $addresses);

        $address = $addresses['data'][0];

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
        $this->assertEquals(2, $addresses['total']);
        $this->assertEquals(1, $addresses['pages']);
    }

    public function testPaginate()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $city = CityFixture::getOne($region);
        $newAddress = AddressFixture::getOne($city, 'wdwdwd', '1111111', 'ewwe', '1');
        $newAddress2 = AddressFixture::getOne($city, 'dwdwdw', '111111', 'weew', '2');

        $array = [$newAddress, $newAddress2];

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $addresses = $repositoryAddress->paginate(1, 2);

        $this->assertNotEmpty($addresses);
        $this->assertIsArray($addresses);
        $this->assertArrayHasKey('data', $addresses);
        $this->assertArrayHasKey('total', $addresses);
        $this->assertArrayHasKey('pages', $addresses);

        $address = $addresses['data'][0];

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
        $this->assertEquals(2, $addresses['total']);
        $this->assertEquals(1, $addresses['pages']);
    }

    public function testSearch()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $city = CityFixture::getOne($region);
        $newAddress = AddressFixture::getOne($city, 'wdwdwd', '1111111', 'ewwe', '1');
        $repositoryMock = new AddressRepository($this->entityManager, $this->getMockFinder([$newAddress]), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryMock);
        $addresses = $repositoryMock->search('wdwdwd');

        $this->assertNotEmpty($addresses);
        $this->assertIsArray($addresses);
        $address = reset($addresses);

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());

    }

}