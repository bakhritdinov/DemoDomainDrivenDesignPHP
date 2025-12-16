<?php

namespace App\Tests\Application\City\Query;

use App\Application\City\Query\FindAllCitiesQuery;
use App\Application\City\Query\FindAllCitiesQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
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
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class FindAllCitiesQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindAllCitiesQuery()
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindAllCitiesQueryHandler::class)
        );
    }

    public function testFindAllCountriesQueryHandler()
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

        $cities = $container->get(FindAllCitiesQueryHandler::class)(
            new FindAllCitiesQuery()
        );

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