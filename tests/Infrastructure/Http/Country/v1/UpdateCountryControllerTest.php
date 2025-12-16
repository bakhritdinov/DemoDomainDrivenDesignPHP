<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class UpdateCountryControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testUpdateCountryNameRoute()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);

        $this->assertEquals('Russia', $country->getName());

        $this->client
            ->request(
                'PUT',
                "/api/v1/country/{$country->getId()->toRfc4122()}",
                [
                    'name' => 'updated country'
                ]
            );

        self::assertResponseIsSuccessful();

        $country = $repositoryCountry->ofId($country->getId());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('updated country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNotNull($country->getUpdatedAt());
    }

    public function testUpdateCountryIsActiveRoute()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);

        $this->assertTrue($country->isActive());

        $this->client
            ->request(
                'PUT',
                "/api/v1/country/{$country->getId()->toRfc4122()}",
                [
                    'isActive' => false
                ]
            );

        self::assertResponseIsSuccessful();

        $country = $repositoryCountry->ofIdDeactivated($country->getId());

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