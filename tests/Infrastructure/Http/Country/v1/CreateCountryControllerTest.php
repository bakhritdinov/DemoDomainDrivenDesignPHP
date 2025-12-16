<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\HttpTestCase;

class CreateCountryControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateCountryRoute()
    {
        $container = $this->getContainer();
        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);

        $this->client
            ->request(
                'POST',
                '/api/v1/country',
                [
                    'name' => 'test country',
                    'numericCode' => 643,
                    'alpha2' => 'RU',
                    'alpha3' => 'RUS',
                ]
            );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

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

        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);

        $country = $repository->ofAlpha2('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }
}