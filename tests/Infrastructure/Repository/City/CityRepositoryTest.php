<?php

namespace App\Tests\Infrastructure\Repository\City;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;

class CityRepositoryTest extends DoctrineTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateCity()
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

        $city = $repositoryCity->ofId($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($newCity->getId(), $city->getId());
        $this->assertEquals($newCity->getName(), $city->getName());
        $this->assertEquals($newCity->getType(), $city->getType());
    }

    public function testUpdateCity()
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

        $this->assertEquals('Moscow', $newCity->getName());

        $city = $repositoryCity->ofId($newCity->getId());

        $city->changeName('Moscow2');
        $repositoryCity->update($city);

        $updatedCity = $repositoryCity->ofId($newCity->getId());

        $this->assertNotNull($updatedCity->getUpdatedAt());
        $this->assertEquals('Moscow2', $updatedCity->getName());

        $this->assertTrue($updatedCity->isActive());

        $updatedCity->changeIsActive(false);
        $repositoryCity->update($updatedCity);

        $deactivatedCity = $repositoryCity->ofIdDeactivated($updatedCity->getId());

        $this->assertNotNull($deactivatedCity);
        $this->assertFalse($deactivatedCity->isActive());
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
        $newCity = CityFixture::getOne($region);
        $repositoryCity->create($newCity);

        $city = $repositoryCity->ofId($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($newCity->getId(), $city->getId());
        $this->assertEquals($newCity->getName(), $city->getName());
        $this->assertEquals($newCity->getType(), $city->getType());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
    }

    public function testOfRegionAndTypeAndName()
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

        $city = $repositoryCity->ofRegionAndTypeAndName($region, 'city', 'Moscow');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($newCity->getId(), $city->getId());
        $this->assertEquals($newCity->getName(), $city->getName());
        $this->assertEquals($newCity->getType(), $city->getType());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
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
        $newCity = CityFixture::getOne($region);
        $newCity->changeIsActive(false);
        $repositoryCity->create($newCity);

        $deactivatedCity = $repositoryCity->ofIdDeactivated($newCity->getId());

        $this->assertNotNull($deactivatedCity);
        $this->assertInstanceOf(City::class, $deactivatedCity);
        $this->assertEquals($newCity->getId(), $deactivatedCity->getId());
        $this->assertEquals($newCity->getName(), $deactivatedCity->getName());
        $this->assertEquals($newCity->getType(), $deactivatedCity->getType());
        $this->assertFalse($deactivatedCity->isActive());
        $this->assertNotNull($deactivatedCity->getCreatedAt());
        $this->assertNotNull($deactivatedCity->getUpdatedAt());
    }

    public function testOfRegionAndTypeAndNameDeactivated()
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
        $newCity->changeIsActive(false);
        $repositoryCity->create($newCity);

        $deactivatedCity = $repositoryCity->ofRegionAndTypeAndNameDeactivated($region, $newCity->getType(), $newCity->getName());

        $this->assertNotNull($deactivatedCity);
        $this->assertInstanceOf(City::class, $deactivatedCity);
        $this->assertEquals($newCity->getId(), $deactivatedCity->getId());
        $this->assertEquals($newCity->getName(), $deactivatedCity->getName());
        $this->assertEquals($newCity->getType(), $deactivatedCity->getType());
        $this->assertFalse($deactivatedCity->isActive());
        $this->assertNotNull($deactivatedCity->getCreatedAt());
        $this->assertNotNull($deactivatedCity->getUpdatedAt());
    }

    public function testPaginate()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();

        $region = RegionFixture::getOne($country);

        $newCity = CityFixture::getOne($region);
        $newCity2 = CityFixture::getOne($region, 'city', 'Moscow2');

        $array = [$newCity, $newCity2];

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);

        $cities = $repositoryCity->paginate(1, 2);

        $this->assertNotEmpty($cities);
        $this->assertIsArray($cities);
        $this->assertArrayHasKey('data', $cities);
        $this->assertArrayHasKey('total', $cities);
        $this->assertArrayHasKey('pages', $cities);

        $city = $cities['data'][0];

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moscow', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
        $this->assertEquals(2, $cities['total']);
        $this->assertEquals(1, $cities['pages']);
    }

    public function testSearch()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $newCity = CityFixture::getOne($region);
        $repositoryMock = new CityRepository($this->entityManager, $this->getMockFinder([$newCity]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryMock);
        $cities = $repositoryMock->search('Moscow');

        $this->assertNotEmpty($cities);
        $this->assertIsArray($cities);
        $city = reset($cities);

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moscow', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());

    }
}