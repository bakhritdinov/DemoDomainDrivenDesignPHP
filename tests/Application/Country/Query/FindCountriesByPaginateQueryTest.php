<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountriesByPaginateQuery;
use App\Application\Country\Query\FindCountriesByPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class FindCountriesByPaginateQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountriesByPaginateQuery(1, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountriesByPaginateQueryHandler::class)
        );
    }

    public function testFindCountriesByPaginateQueryHandler()
    {
        $container = $this->getContainer();

        $newCountry = CountryFixture::getOne();
        $newCountry2 = CountryFixture::getOne('test country2', 363, 'KZ', 'KZZ');

        $array = [$newCountry, $newCountry2];

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);

        $countries = $container->get(FindCountriesByPaginateQueryHandler::class)(
            new FindCountriesByPaginateQuery(1, 2)
        );

        $this->assertNotEmpty($countries);
        $this->assertIsArray($countries);
        $this->assertArrayHasKey('data', $countries);
        $this->assertArrayHasKey('total', $countries);
        $this->assertArrayHasKey('pages', $countries);

        $country = $countries['data'][0];

        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('Russia', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());

        $this->assertEquals(2, $countries['total']);
        $this->assertEquals(1, $countries['pages']);
    }
}