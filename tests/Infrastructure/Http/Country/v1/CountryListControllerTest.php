<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class CountryListControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testPaginate()
    {
        $container = $this->getContainer();

        $newCountry = CountryFixture::getOne();
        $newCountry2 = CountryFixture::getOne('test country2', 363, 'KZ', 'KZZ');

        $array = [$newCountry, $newCountry2];

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);

        $this->client
            ->request(
            'GET',
            '/api/v1/country/paginate?page=1&offset=2'
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
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

        $this->assertStringContainsString('RU', $response);
    }

    public function testSearchRoute()
    {
        $container = $this->getContainer();

        $newCountry = CountryFixture::getOne();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([$newCountry]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);

        $this->client
            ->request(
            'GET',
            "/api/v1/country/search?query=RU"
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