<?php

namespace App\Tests\Application\Region\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\Region\Query\FindRegionByNameDeactivatedQuery;
use App\Application\Region\Query\FindRegionByNameDeactivatedQueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class FindRegionByNameDeactivatedQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindRegionByNameDeactivatedQuery('Moscow')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindRegionByNameDeactivatedQueryHandler::class)
        );
    }

    public function testFindRegionByNameDeactivatedQueryHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $region = RegionFixture::getOne($country, isActive: false);
        $repositoryRegion->create($region);

        $region = $container->get(FindRegionByNameDeactivatedQueryHandler::class)(
            new FindRegionByNameDeactivatedQuery($region->getName())
        );

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
    }
}