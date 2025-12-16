<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class CreateRegionControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateRegionRoute()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $country = $repositoryCountry->ofNumericCode($country->getNumericCode());

        $this->client
            ->request(
                'POST',
                '/api/v1/region',
                [
                    'countryAlpha2' => $country->getAlpha2(),
                    'name' => 'Moscow',
                    'code' => 'RU-MOW',
                ]
            );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('Moscow', $response);
        $this->assertStringContainsString('RU-MOW', $response);

        $repository = $this->getContainer()->get(RegionRepositoryInterface::class);

        $region = $repository->ofCode('RU-MOW');

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moscow', $region->getName());
        $this->assertEquals('RU-MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
    }
}