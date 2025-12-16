<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class FindCountryControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testFindById()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);

        $this->client
            ->request(
            'GET',
            "/api/v1/country/find-by-id/{$country->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('numericCode', $response);
        $this->assertStringContainsString('alpha2', $response);
        $this->assertStringContainsString('alpha3', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
    }

    public function testFindByCode()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne();
        $repositoryCountry->create($newCountry);

        $this->client
            ->request(
            'GET',
            "/api/v1/country/find-by-numeric-code/{$newCountry->getNumericCode()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('numericCode', $response);
        $this->assertStringContainsString('alpha2', $response);
        $this->assertStringContainsString('alpha3', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
    }

    public function testFindByName()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne();
        $repositoryCountry->create($newCountry);

        $this->client
            ->request(
            'GET',
            "/api/v1/country/find-by-name/{$newCountry->getName()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('numericCode', $response);
        $this->assertStringContainsString('alpha2', $response);
        $this->assertStringContainsString('alpha3', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
    }
}