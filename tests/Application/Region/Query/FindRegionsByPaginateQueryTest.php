<?php

namespace App\Tests\Application\Region\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Region\Query\FindRegionsByPaginateQuery;
use App\Application\Region\Query\FindRegionsByPaginateQueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindRegionsByPaginateQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindRegionsByPaginateQuery(1, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindRegionsByPaginateQueryHandler::class)
        );
    }

    public function testFindCountriesByPaginateQueryHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();

        $newRegion = RegionFixture::getOne($country, 'Moscow', 'RU-MOW', true, Uuid::v1());
        $newRegion2 = RegionFixture::getOne($country, 'Moscow1', 'AMU', true, Uuid::v1());

        $array = [$newRegion, $newRegion2];

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $regions = $container->get(FindRegionsByPaginateQueryHandler::class)(
            new FindRegionsByPaginateQuery(1, 2)
        );

        $this->assertNotEmpty($regions);
        $this->assertIsArray($regions);
        $this->assertArrayHasKey('data', $regions);
        $this->assertArrayHasKey('total', $regions);
        $this->assertArrayHasKey('pages', $regions);

        $region = $regions['data'][0];

        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());

        $this->assertEquals(2, $regions['total']);
        $this->assertEquals(1, $regions['pages']);
    }

}