<?php

namespace App\Tests\Core\Domain\City\Entity;

use App\Core\Domain\Region\Entity\Region;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CityTest extends KernelTestCase
{
    public function testCreateCity()
    {
        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);

        $city = CityFixture::getOne($region);

        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('city', $city->getType());
        $this->assertEquals('Moscow', $city->getName());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
    }

    public function testUpdateCityName()
    {
        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);

        $city = CityFixture::getOne($region);

        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('city', $city->getType());
        $this->assertEquals('Moscow', $city->getName());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());

        $city->changeName('Moscow2');

        $this->assertEquals('Moscow2', $city->getName());
        $this->assertNotNull($city->getUpdatedAt());
    }

    public function testUpdateCityIsActive()
    {
        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);

        $city = CityFixture::getOne($region);

        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('city', $city->getType());
        $this->assertEquals('Moscow', $city->getName());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());

        $city->changeIsActive(false);

        $this->assertFalse($city->isActive());
        $this->assertNotNull($city->getUpdatedAt());
    }
}