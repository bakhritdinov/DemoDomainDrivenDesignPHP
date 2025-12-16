<?php

namespace App\Tests\Core\Domain\Region\Entity;

use App\Core\Domain\Country\Entity\Country;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegionTest extends KernelTestCase
{
    public function testCreateRegion()
    {
        $country = CountryFixture::getOne();

        $region = RegionFixture::getOne($country);

        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
    }

    public function testUpdateRegionName()
    {
        $country = CountryFixture::getOne();

        $region = RegionFixture::getOne($country);

        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());

        $region->changeName('Moscow2');

        $this->assertEquals('Moscow2', $region->getName());
        $this->assertNotNull($region->getUpdatedAt());
    }

    public function testUpdateRegionIsActive()
    {
        $country = CountryFixture::getOne();

        $region = RegionFixture::getOne($country);

        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());

        $region->changeIsActive(false);

        $this->assertFalse($region->isActive());
        $this->assertNotNull($region->getUpdatedAt());
    }
}