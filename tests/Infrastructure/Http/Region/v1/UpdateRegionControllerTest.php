<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class UpdateRegionControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testUpdateRegionNameRoute()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $newRegion = RegionFixture::getOne($country);

        $repositoryRegion->create($newRegion);

        $this->assertEquals('Moscow', $newRegion->getName());

        $this->client
            ->request(
                'PUT',
                "/api/v1/region/{$newRegion->getId()->toRfc4122()}",
                [
                    'name' => 'Moscow2'
                ]
            );

        self::assertResponseIsSuccessful();

        $region = $repositoryRegion->ofId($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals('Moscow2', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
    }

    public function testUpdateRegionIsActiveRoute()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $newRegion = RegionFixture::getOne($country);

        $repositoryRegion->create($newRegion);

        $this->assertTrue($newRegion->isActive());

        $this->client
            ->request(
                'PUT',
                "/api/v1/region/{$newRegion->getId()->toRfc4122()}",
                [
                    'isActive' => false
                ]
            );

        self::assertResponseIsSuccessful();

        $region = $repositoryRegion->ofIdDeactivated($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertFalse($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
    }
}