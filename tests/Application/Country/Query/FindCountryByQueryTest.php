<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountryByQuery;
use App\Application\Country\Query\FindCountryByQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindCountryByQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountryByQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountryByQueryHandler::class)
        );
    }

    public function testFindCountryByQueryHandler()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([$country]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $repositoryCountry->create($country);

        $countries = $container->get(FindCountryByQueryHandler::class)(
            new FindCountryByQuery($country->getId())
        );

        $country = reset($countries);
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