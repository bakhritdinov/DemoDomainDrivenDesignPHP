<?php

namespace App\Tests\Application\Country\Query;

use App\Application\Country\Query\FindCountryByIdQuery;
use App\Application\Country\Query\FindCountryByIdQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindCountryByIdQueryTest extends MessageBusTestCase
{
    use ElasticSearchMockTrait;

    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCountryByIdQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCountryByIdQueryHandler::class)
        );
    }

    public function testFindCountryByIdQueryHandler()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);

        $country = $container->get(FindCountryByIdQueryHandler::class)(
            new FindCountryByIdQuery($country->getId())
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