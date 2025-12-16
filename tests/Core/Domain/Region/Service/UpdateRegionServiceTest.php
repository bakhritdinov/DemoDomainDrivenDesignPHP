<?php

namespace App\Tests\Core\Domain\Region\Service;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Exception\RegionNotFoundException;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Core\Domain\Region\Service\UpdateRegionService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Region\UpdateRegionDtoFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateRegionServiceTest extends KernelTestCase
{
    private RegionRepositoryInterface $regionRepository;
    private CountryRepositoryInterface $countryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->regionRepository = $this->createMock(RegionRepositoryInterface::class);
        $this->countryRepository = $this->createMock(CountryRepositoryInterface::class);
    }

    public function testUpdateRegionName()
    {
        $country = CountryFixture::getOne();

        $oldRegion = RegionFixture::getOne($country);
        $this->regionRepository->method('ofId')->willReturn($oldRegion);

        $service = new UpdateRegionService($this->regionRepository, $this->countryRepository);

        $service->update($oldRegion->getId(), UpdateRegionDtoFixture::getOne(name: 'Moscow2'));

        $this->regionRepository->method('ofId')->willReturn(RegionFixture::getOne(
            $country, 'Moscow2', 'RU-MOW', id: $oldRegion->getId()
        ));

        $newRegion = $this->regionRepository->ofId($oldRegion->getId());

        $this->assertNotNull($newRegion);
        $this->assertInstanceOf(Region::class, $newRegion);
        $this->assertNotNull($newRegion->getCountry());
        $this->assertInstanceOf(Country::class, $newRegion->getCountry());
        $this->assertEquals('Moscow2', $newRegion->getName());
        $this->assertEquals('RU-MOW', $newRegion->getCode());
        $this->assertNotNull($newRegion->getCreatedAt());
        $this->assertNotNull($newRegion->getUpdatedAt());
    }

    public function testUpdateRegionNameIfNotFound()
    {
        $this->regionRepository->method('ofId')->willReturn(null);

        $service = new UpdateRegionService($this->regionRepository, $this->countryRepository);

        $this->expectException(RegionNotFoundException::class);
        $service->update(Uuid::v1(), UpdateRegionDtoFixture::getOne(name: 'Moscow'));
    }

    public function testUpdateRegionIsActive()
    {
        $country = CountryFixture::getOne();

        $oldRegion = RegionFixture::getOne($country);
        $this->regionRepository->method('ofId')->willReturn($oldRegion);

        $service = new UpdateRegionService($this->regionRepository, $this->countryRepository);

        $this->assertTrue($oldRegion->isActive());

        $service->update($oldRegion->getId(),UpdateRegionDtoFixture::getOne(isActive: false));

        $this->regionRepository->method('ofId')->willReturn(RegionFixture::getOne(
            $country, 'Moscow', 'RU-MOW', false, $oldRegion->getId()
        ));

        $newRegion = $this->regionRepository->ofId($oldRegion->getId());

        $this->assertNotNull($newRegion);
        $this->assertInstanceOf(Region::class, $newRegion);
        $this->assertNotNull($newRegion->getCountry());
        $this->assertInstanceOf(Country::class, $newRegion->getCountry());
        $this->assertEquals('Moscow', $newRegion->getName());
        $this->assertEquals('RU-MOW', $newRegion->getCode());
        $this->assertFalse($newRegion->isActive());
        $this->assertNotNull($newRegion->getCreatedAt());
        $this->assertNotNull($newRegion->getUpdatedAt());
    }

    public function testUpdateRegionIsActiveIfNotFound()
    {
        $service = new UpdateRegionService($this->regionRepository, $this->countryRepository);

        $this->expectException(RegionNotFoundException::class);
        $service->update(Uuid::v1(), UpdateRegionDtoFixture::getOne());
    }
}