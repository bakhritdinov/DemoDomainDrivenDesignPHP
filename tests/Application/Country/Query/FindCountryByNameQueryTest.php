<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountryByNameQuery;
use App\Application\Country\Query\FindCountryByNameQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class FindCountryByNameQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountryByNameQuery('test country')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountryByNameQueryHandler::class)
        );
    }

    public function testFindCountryByNameQueryHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $country = $container->get(FindCountryByNameQueryHandler::class)(
            new FindCountryByNameQuery($country->getName())
        );

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
}