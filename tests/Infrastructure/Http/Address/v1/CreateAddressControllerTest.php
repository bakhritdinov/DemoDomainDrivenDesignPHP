<?php

namespace App\Tests\Infrastructure\Http\Address\v1;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Address\AddressRepository;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\HttpTestCase;

class CreateAddressControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateAddressRoute()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $this->assertEmpty($repositoryAddress->all());

        $service = $this->createMock(FindExternalAddressInterface::class);
        $service->method('find')->willReturn(DaDataAddressDtoFixture::getOne());
        $container->set(FindExternalAddressInterface::class, $service);

        $this->client
            ->request(
                'POST',
                '/api/v1/address',
                [
                    'address' => '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1',
                ]
            );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);


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
        $this->assertStringContainsString('RU-BEL', $response);
        $this->assertStringContainsString('309850', $response);
        $this->assertStringContainsString('1', $response);
        $this->assertStringContainsString('2', $response);

        $address = $repositoryAddress->ofAddress('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1');

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
    }
}