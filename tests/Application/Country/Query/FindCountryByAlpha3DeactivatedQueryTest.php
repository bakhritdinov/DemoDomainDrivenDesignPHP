<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountryByAlpha3DeactivatedQuery;
use App\Application\Country\Query\FindCountryByAlpha3DeactivatedQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;

class FindCountryByAlpha3DeactivatedQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountryByAlpha3DeactivatedQuery("RU")
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountryByAlpha3DeactivatedQueryHandler::class)
        );
    }

    public function testFindCountryByAlpha3DeactivatedQueryHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne(isActive: false);
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofIdDeactivated($country->getId());

        $country = $container->get(FindCountryByAlpha3DeactivatedQueryHandler::class)(
            new FindCountryByAlpha3DeactivatedQuery($country->getAlpha3())
        );

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('Russia', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertFalse($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNotNull($country->getUpdatedAt());
    }
}