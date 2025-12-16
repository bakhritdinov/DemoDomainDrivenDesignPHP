<?php

namespace App\Tests\Core\Domain\Address\Service;

use App\Core\Domain\Address\Exception\AddressAlreadyCreatedException;
use App\Core\Domain\Address\Exception\AddressDeactivatedException;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\Address\Service\CreateAddressService;
use App\Core\Domain\Address\ValueObject\Point;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateAddressServiceTest extends KernelTestCase
{
    public function testCreateAddress()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);
        $addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);

        $addressService = new CreateAddressService($addressRepositoryMock, $regionRepositoryMock, $cityRepositoryMock);

        $countryRepositoryMock->method('ofAlpha2')
            ->willReturn(CountryFixture::getOne());
        $country = $countryRepositoryMock->ofAlpha2('RU');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country));
        $region = $regionRepositoryMock->ofCode('RU-MOW');

        $cityRepositoryMock->method('ofRegionAndTypeAndName')
            ->willReturn(CityFixture::getOne($region, 'город', 'Алексеевка'));
        $city = $cityRepositoryMock->ofRegionAndTypeAndName($region, 'город', 'Алексеевка');

        $addressDto = DaDataAddressDtoFixture::getOne();

        $addressService->create($addressDto);
        $addressString = '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1';
        $addressRepositoryMock->method('ofAddress')
            ->willReturn(AddressFixture::getOneFromAddressDto($city, $addressDto));
        $address = $addressRepositoryMock->ofAddress($addressString);

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
    }

    public function testCreateDeactivatedAddress()
    {
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);

        $addressService = new CreateAddressService($addressRepositoryMock, $regionRepositoryMock, $cityRepositoryMock);

        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $city = CityFixture::getOne($region);

        $regionRepositoryMock->method('ofCode')->willReturn($region);

        $cityRepositoryMock->method('ofRegionAndTypeAndName')->willReturn($city);

        $addressDto = DaDataAddressDtoFixture::getOne();

        $addressRepositoryMock->method('ofAddressDeactivated')
            ->willReturn(AddressFixture::getOneFromAddressDto($city, $addressDto, false));

        $this->expectException(AddressDeactivatedException::class);
        $addressService->create($addressDto);
    }
}