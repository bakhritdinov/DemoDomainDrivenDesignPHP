<?php

namespace App\Tests\Core\Domain\City\Service;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Exception\CityAlreadyCreatedException;
use App\Core\Domain\City\Exception\CityDeactivatedException;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\City\Service\CreateCityService;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Core\Domain\Region\Service\CreateRegionService;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\City\CreateCityDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\CreateRegionDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCityServiceTest extends KernelTestCase
{
    public function testCreateCity()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);

        $regionService = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);
        $cityService = new CreateCityService($cityRepositoryMock, $regionRepositoryMock);

        $countryRepositoryMock->method('ofAlpha2')
            ->willReturn(CountryFixture::getOne());
        $country = $countryRepositoryMock->ofAlpha2('RU');
        $regionService->create(CreateRegionDtoFixture::getOne($country->getAlpha2()));

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country));
        $region = $regionRepositoryMock->ofCode('RU-MOW');
        $cityService->create(CreateCityDtoFixture::getOne($region->getCode()));

        $cityRepositoryMock->method('ofRegionAndTypeAndName')
            ->willReturn(CityFixture::getOne($region));

        $city = $cityRepositoryMock->ofRegionAndTypeAndName($region, 'city', 'Moscow');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moscow', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
    }

    public function testCreateDeactivatedCity()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);

        $regionService = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);
        $cityService = new CreateCityService($cityRepositoryMock, $regionRepositoryMock);

        $countryRepositoryMock->method('ofAlpha2')
            ->willReturn(CountryFixture::getOne());
        $country = $countryRepositoryMock->ofAlpha2('RU');
        $regionService->create(CreateRegionDtoFixture::getOne($country->getAlpha2()));

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country));
        $region = $regionRepositoryMock->ofCode('RU-MOW');
        $cityService->create(CreateCityDtoFixture::getOne($region->getCode()));

        $cityRepositoryMock->method('ofRegionAndTypeAndNameDeactivated')
            ->willReturn(CityFixture::getOne($region, isActive: false));


        $this->expectException(CityDeactivatedException::class);
        $cityService->create(CreateCityDtoFixture::getOne($region->getCode()));
    }
}