<?php

namespace App\Tests\Infrastructure\Repository\Region;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Component\Uid\Uuid;

class RegionRepositoryTest extends DoctrineTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateRegion()
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

        $region = $repositoryRegion->ofId($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
    }

    public function testUpdateRegion()
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

        $region = $repositoryRegion->ofId($newRegion->getId());

        $region->changeName('Moscow2');
        $repositoryRegion->update($region);

        $updatedRegion = $repositoryRegion->ofId($newRegion->getId());

        $this->assertNotNull($updatedRegion->getUpdatedAt());
        $this->assertEquals('Moscow2', $updatedRegion->getName());

        $this->assertTrue($updatedRegion->isActive());

        $updatedRegion->changeIsActive(false);
        $repositoryRegion->update($updatedRegion);

        $deactivatedRegion = $repositoryRegion->ofIdDeactivated($updatedRegion->getId());

        $this->assertNotNull($deactivatedRegion);
        $this->assertFalse($deactivatedRegion->isActive());
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
        $newRegion = RegionFixture::getOne($country);
        $repositoryRegion->create($newRegion);

        $region = $repositoryRegion->ofId($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
    }

    public function testOfCode()
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

        $region = $repositoryRegion->ofCode($newRegion->getCode());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
    }

    public function testOfName()
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

        $region = $repositoryRegion->ofName($newRegion->getName());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
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
        $newRegion = RegionFixture::getOne($country);
        $newRegion->changeIsActive(false);
        $repositoryRegion->create($newRegion);

        $deactivatedRegion = $repositoryRegion->ofIdDeactivated($newRegion->getId());

        $this->assertNotNull($deactivatedRegion);
        $this->assertInstanceOf(Region::class, $deactivatedRegion);
        $this->assertEquals($newRegion->getId(), $deactivatedRegion->getId());
        $this->assertEquals($newRegion->getName(), $deactivatedRegion->getName());
        $this->assertEquals($newRegion->getCode(), $deactivatedRegion->getCode());
        $this->assertFalse($deactivatedRegion->isActive());
        $this->assertNotNull($deactivatedRegion->getCreatedAt());
        $this->assertNotNull($deactivatedRegion->getUpdatedAt());
    }

    public function testOfCodeDeactivated()
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
        $newRegion->changeIsActive(false);
        $repositoryRegion->create($newRegion);

        $deactivatedRegion = $repositoryRegion->ofCodeDeactivated($newRegion->getCode());

        $this->assertNotNull($deactivatedRegion);
        $this->assertInstanceOf(Region::class, $deactivatedRegion);
        $this->assertEquals($newRegion->getId(), $deactivatedRegion->getId());
        $this->assertEquals($newRegion->getName(), $deactivatedRegion->getName());
        $this->assertEquals($newRegion->getCode(), $deactivatedRegion->getCode());
        $this->assertFalse($deactivatedRegion->isActive());
        $this->assertNotNull($deactivatedRegion->getCreatedAt());
        $this->assertNotNull($deactivatedRegion->getUpdatedAt());
    }

    public function testPaginated()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();

        $newRegion = RegionFixture::getOne($country);
        $newRegion2 = RegionFixture::getOne($country, 'Moscow1', 'AMU', true, Uuid::v1());

        $array = [$newRegion, $newRegion2];

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $regions = $repositoryRegion->paginate(1, 2);

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

    public function testSearch()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $newRegion = RegionFixture::getOne($country);

        $repositoryMock = new RegionRepository($this->entityManager, $this->getMockFinder([$newRegion]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryMock);
        $regions = $repositoryMock->search('RU-MOW');

        $this->assertNotEmpty($regions);
        $this->assertIsArray($regions);
        $region = reset($regions);

        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());

    }
}