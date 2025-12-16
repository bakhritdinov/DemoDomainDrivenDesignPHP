<?php

namespace App\Tests\Core\Domain\Country\Service;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Exception\CountryAlreadyCreatedException;
use App\Core\Domain\Country\Exception\CountryDeactivatedException;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Country\Service\CreateCountryService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Country\CreateCountryDtoFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCountryServiceTest extends KernelTestCase
{
    public function testCreateCountry()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);
        
        $createCountryDto = CreateCountryDtoFixture::getOne();

        $service->create($createCountryDto);

        $repositoryMock->method('ofAlpha2')
            ->willReturn(CountryFixture::getOne());

        $country = $repositoryMock->ofAlpha2('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('Russia', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

    public function testCreateDeactivatedOfNumericCodeCountry()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);

        $repositoryMock->method('ofNumericCodeDeactivated')
            ->willReturn(CountryFixture::getOne(isActive:  false));

        $this->expectException(CountryDeactivatedException::class);
        $service->create(CreateCountryDtoFixture::getOne());
    }

    public function testCreateDeactivatedOfAlpha2Country()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);

        $repositoryMock->method('ofAlpha2Deactivated')
            ->willReturn(CountryFixture::getOne(isActive:  false));

        $this->expectException(CountryDeactivatedException::class);
        $service->create(CreateCountryDtoFixture::getOne());
    }

    public function testCreateDeactivatedOfAlpha3Country()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);

        $repositoryMock->method('ofAlpha3Deactivated')
            ->willReturn(CountryFixture::getOne(isActive:  false));

        $this->expectException(CountryDeactivatedException::class);
        $service->create(CreateCountryDtoFixture::getOne());
    }
}