<?php

namespace App\Tests\Core\Domain\Country\Entity;

use App\Tests\Fixture\Country\CountryFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CountryTest extends KernelTestCase
{
    public function testCreateCountry()
    {
        $country = CountryFixture::getOne('test country');

        $this->assertEquals('test country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
    }

    public function testUpdateCountryName()
    {
        $country = CountryFixture::getOne('test country');

        $this->assertEquals('test country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());

        $country->changeName('new country');

        $this->assertEquals('new country', $country->getName());
        $this->assertNotNull($country->getUpdatedAt());
    }

    public function testUpdateCountryIsActive()
    {
        $country = CountryFixture::getOne('test country');

        $this->assertEquals('test country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());

        $country->changeIsActive(false);

        $this->assertFalse($country->isActive());
        $this->assertNotNull($country->getUpdatedAt());
    }
}