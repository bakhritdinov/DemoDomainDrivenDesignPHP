<?php

namespace App\Tests\Core\Domain\Region\Service;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Exception\RegionAlreadyCreatedException;
use App\Core\Domain\Region\Exception\RegionDeactivatedException;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Core\Domain\Region\Service\CreateRegionService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\CreateRegionDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateRegionServiceTest extends KernelTestCase
{
    public function testCreateRegion()
    {
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofAlpha2')
            ->willReturn(CountryFixture::getOne());
        $country = $countryRepositoryMock->ofAlpha2('RU');
        $service->create(CreateRegionDtoFixture::getOne());

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country));

        $region = $regionRepositoryMock->ofCode('RU-MOW');

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNotNull($region->getUpdatedAt());
    }

    public function testCreateDeactivatedRegion()
    {
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofAlpha2')
            ->willReturn(CountryFixture::getOne());
        $country = $countryRepositoryMock->ofAlpha2('RU');
        $service->create(CreateRegionDtoFixture::getOne());

        $regionRepositoryMock->method('ofCodeDeactivated')
            ->willReturn(RegionFixture::getOne($country, isActive: false));

        $this->expectException(RegionDeactivatedException::class);
        $service->create(CreateRegionDtoFixture::getOne());
    }
}