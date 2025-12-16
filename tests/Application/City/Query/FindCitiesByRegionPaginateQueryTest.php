<?php

namespace App\Tests\Application\City\Query;

use App\Application\City\Query\FindCitiesByRegionPaginateQuery;
use App\Application\City\Query\FindCitiesByRegionPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Infrastructure\Repository\City\CityRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class FindCitiesByRegionPaginateQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $this->assertInstanceOf(
            Query::class,
            new FindCitiesByRegionPaginateQuery($region, 1, 2)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCitiesByRegionPaginateQueryHandler::class)
        );
    }

    public function testFindCitiesByCountryQueryHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();

        $region = RegionFixture::getOne($country);

        $newCity = CityFixture::getOne($region);
        $newCity2 = CityFixture::getOne($region, 'city', 'Moscow2');

        $array = [$newCity, $newCity2];

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);

        $cities = $container->get(FindCitiesByRegionPaginateQueryHandler::class)(
            new FindCitiesByRegionPaginateQuery($region, 1, 2)
        );

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
}