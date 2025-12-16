<?php

namespace App\Tests\Core\Domain\City\Service;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Exception\CityNotFoundException;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\City\Service\UpdateCityService;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\City\UpdateCityDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCityServiceTest extends KernelTestCase
{
    private CityRepositoryInterface $cityRepository;
    private RegionRepositoryInterface $regionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        $this->regionRepository = $this->createMock(RegionRepositoryInterface::class);
    }

    public function testUpdateCityName()
    {
        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $oldCity = CityFixture::getOne($region);

        $this->cityRepository->method('ofId')->willReturn($oldCity);

        $service = new UpdateCityService($this->cityRepository, $this->regionRepository);

        $service->update($oldCity->getId(), UpdateCityDtoFixture::getOne(type: 'village', name: 'Moscow2'));

        $this->cityRepository->method('ofId')->willReturn(CityFixture::getOne(
            $region, 'village', 'Moscow2', true, $oldCity->getId()
        ));

        $newCity = $this->cityRepository->ofId($oldCity->getId());

        $this->assertNotNull($newCity);
        $this->assertInstanceOf(City::class, $newCity);
        $this->assertNotNull($newCity->getRegion());
        $this->assertInstanceOf(Region::class, $newCity->getRegion());
        $this->assertEquals('Moscow2', $newCity->getName());
        $this->assertEquals('village', $newCity->getType());
        $this->assertNotNull($newCity->getCreatedAt());
        $this->assertNotNull($newCity->getUpdatedAt());
    }

    public function testUpdateCityNameIfNotFound()
    {
        $this->cityRepository->method('ofId')->willReturn(null);

        $service = new UpdateCityService($this->cityRepository, $this->regionRepository);

        $this->expectException(CityNotFoundException::class);
        $service->update(Uuid::v1(), UpdateCityDtoFixture::getOne());
    }

    public function testUpdateCityIsActive()
    {
        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $oldCity = CityFixture::getOne($region);

        $this->cityRepository->method('ofId')->willReturn($oldCity);

        $service = new UpdateCityService($this->cityRepository, $this->regionRepository);

        $this->assertTrue($oldCity->isActive());

        $service->update($oldCity->getId(), UpdateCityDtoFixture::getOne(isActive: false));

        $this->cityRepository->method('ofId')->willReturn(CityFixture::getOne(
            $region, 'city', 'Moscow', false, $oldCity->getId()
        ));

        $newCity = $this->cityRepository->ofId($oldCity->getId());

        $this->assertNotNull($newCity);
        $this->assertInstanceOf(City::class, $newCity);
        $this->assertNotNull($newCity->getRegion());
        $this->assertInstanceOf(Region::class, $newCity->getRegion());
        $this->assertEquals('Moscow', $newCity->getName());
        $this->assertEquals('city', $newCity->getType());
        $this->assertNotNull($newCity->getCreatedAt());
        $this->assertNotNull($newCity->getUpdatedAt());
    }

    public function testUpdateCityIsActiveIfNotFound()
    {
        $service = new UpdateCityService($this->cityRepository, $this->regionRepository);

        $this->expectException(CityNotFoundException::class);
        $service->update(Uuid::v1(), UpdateCityDtoFixture::getOne());
    }
}