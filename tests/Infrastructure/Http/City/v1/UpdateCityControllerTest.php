<?php

namespace App\Tests\Infrastructure\Http\City\v1;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\User\UserPermissionFixture;
use App\Tests\HttpTestCase;

class UpdateCityControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testUpdateCityNameRoute()
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

        $this->client
            ->request(
                'PUT',
                "/api/v1/city/{$newCity->getId()->toRfc4122()}",
                [
                    'name' => 'Moscow2'
                ]
            );

        self::assertResponseIsSuccessful();

        $city = $repositoryCity->ofId($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals('Moscow2', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
    }

    public function testUpdateCityIsActiveRoute()
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

        $this->assertTrue($newCity->isActive());

        $this->client
            ->request(
                'PUT',
                "/api/v1/city/{$newCity->getId()->toRfc4122()}",
                [
                    'isActive' => false
                ]
            );

        self::assertResponseIsSuccessful();

        $city = $repositoryCity->ofIdDeactivated($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals('Moscow', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertFalse($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
    }
}