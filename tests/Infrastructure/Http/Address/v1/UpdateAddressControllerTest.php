<?php

namespace App\Tests\Infrastructure\Http\Address\v1;

use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Address\AddressRepository;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class UpdateAddressControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testUpdateAddressRoute()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $region = RegionFixture::getOne($country);
        $repositoryRegion->create($region);
        $region = $repositoryRegion->ofId($region->getId());

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);
        $city = CityFixture::getOne($region);
        $repositoryCity->create($city);
        $city = $repositoryCity->ofId($city->getId());

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);
        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $repositoryAddress->create($address);


        $this->assertTrue($address->isActive());

        $this->client
            ->request(
                'PUT',
                "/api/v1/address/{$address->getId()->toRfc4122()}",
                [
                    'isActive' => false
                ]
            );

        self::assertResponseIsSuccessful();

        $addressOld = $repositoryAddress->ofId($address->getId());

        $this->assertNull($addressOld);

        $address = $repositoryAddress->ofIdDeactivated($address->getId());

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertFalse($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNotNull($address->getUpdatedAt());
    }
}