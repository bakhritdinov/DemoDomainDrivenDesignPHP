<?php

namespace App\Tests\Infrastructure\Http\Address\v1;

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
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class AddressListControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testPaginate()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $city = CityFixture::getOne($region);
        $newAddress = AddressFixture::getOne($city, 'wdwdwd', '1111111', 'ewwe', '1');
        $newAddress2 = AddressFixture::getOne($city, 'dwdwdw', '111111', 'weew', '2');

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockPaginate([$newAddress, $newAddress2]), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $this->client
            ->request(
                'GET',
                '/api/v1/address/paginate?page=1&offset=2'
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('postalCode', $response);
        $this->assertStringContainsString('street', $response);
        $this->assertStringContainsString('house', $response);
        $this->assertStringContainsString('flat', $response);
        $this->assertStringContainsString('entrance', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('type', $response);
        $this->assertStringContainsString('floor', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('Moscow', $response);
        $this->assertStringContainsString('RU-MOW', $response);
        $this->assertStringContainsString('111111', $response);
        $this->assertStringContainsString('ewwe', $response);
        $this->assertStringContainsString('1', $response);
        $this->assertStringContainsString('2', $response);
    }

    public function testSearchRoute()
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

        $newAddress = AddressFixture::getOne($city, '111111, Lenina, 1, 1', '111111', 'Lenina', '1', '2');
        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockFinder([$newAddress]), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $this->client
            ->request(
                'GET',
                "/api/v1/address/search?query=Lenina"
            );


        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('postalCode', $response);
        $this->assertStringContainsString('street', $response);
        $this->assertStringContainsString('house', $response);
        $this->assertStringContainsString('flat', $response);
        $this->assertStringContainsString('entrance', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('type', $response);
        $this->assertStringContainsString('floor', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('Moscow', $response);
        $this->assertStringContainsString('RU-MOW', $response);
        $this->assertStringContainsString('111111', $response);
        $this->assertStringContainsString('Lenina', $response);
        $this->assertStringContainsString('1', $response);
        $this->assertStringContainsString('2', $response);

    }

    public function testFindByCity()
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

        $newAddress = AddressFixture::getOne($city, 'wdwdwd', '1111111', 'ewwe', '1');
        $newAddress2 = AddressFixture::getOne($city, 'dwdwdw', '111111', 'weew', '2');

        $array = [$newAddress, $newAddress2];

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $this->client
            ->request(
                'GET',
                "/api/v1/address/find-by-city-id-paginate?cityId={$city->getId()->toRfc4122()}&page=1&offset=2"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('postalCode', $response);
        $this->assertStringContainsString('street', $response);
        $this->assertStringContainsString('house', $response);
        $this->assertStringContainsString('flat', $response);
        $this->assertStringContainsString('entrance', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('type', $response);
        $this->assertStringContainsString('floor', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('Moscow', $response);
        $this->assertStringContainsString('RU-MOW', $response);
        $this->assertStringContainsString('111111', $response);
        $this->assertStringContainsString('ewwe', $response);
        $this->assertStringContainsString('1', $response);
        $this->assertStringContainsString('2', $response);
    }


}