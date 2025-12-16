<?php

namespace App\Tests\Application\Address\Query;

use App\Application\Address\Query\FindAddressesByCityPaginateQuery;
use App\Application\Address\Query\FindAddressesByCityPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\Address\ValueObject\Point;
use App\Infrastructure\Repository\Address\AddressRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;

class FindAddressesByCityQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $city = CityFixture::getOne($region);
        $this->assertInstanceOf(
            Query::class,
            new FindAddressesByCityPaginateQuery($city, 1, 2)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindAddressesByCityPaginateQueryHandler::class)
        );
    }

    public function testFindCitysByCountryQueryHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $region = RegionFixture::getOne($country);
        $city = CityFixture::getOne($region);
        $newAddress = AddressFixture::getOne($city, 'wdwdwd', '1111111', 'ewwe', '1');
        $newAddress2 = AddressFixture::getOne($city, 'dwdwdw', '111111', 'weew', '2');

        $array = [$newAddress, $newAddress2];

        $repositoryAddress = new AddressRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(AddressRepositoryInterface::class, $repositoryAddress);

        $addresses = $container->get(FindAddressesByCityPaginateQueryHandler::class)(
            new FindAddressesByCityPaginateQuery($city, 1, 2)
        );

        $this->assertNotEmpty($addresses);
        $this->assertIsArray($addresses);
        $this->assertArrayHasKey('data', $addresses);
        $this->assertArrayHasKey('total', $addresses);
        $this->assertArrayHasKey('pages', $addresses);

        $address = $addresses['data'][0];

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertInstanceOf(Point::class, $address->getPoint());
        $this->assertEquals(12.122133, $address->getPoint()->getLatitude());
        $this->assertEquals(32.333333, $address->getPoint()->getLongitude());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
        $this->assertEquals(2, $addresses['total']);
        $this->assertEquals(1, $addresses['pages']);

    }
}