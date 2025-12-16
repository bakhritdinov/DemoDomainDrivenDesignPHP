<?php

namespace App\Tests\Infrastructure\Http\City\v1;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class CreateCityControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateCityRoute()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);
        $region = RegionFixture::getOne($country);
        $repositoryRegion->create($region);
        $region = $repositoryRegion->ofId($region->getId());

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);

        $this->client
            ->request(
                'POST',
                '/api/v1/city',
                [
                    'regionCode' => $region->getCode(),
                    'type' => 'city',
                    'name' => 'Moscow',
                ]
            );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('type', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('Moscow', $response);
        $this->assertStringContainsString('RU-MOW', $response);

        $repository = $this->getContainer()->get(CityRepositoryInterface::class);

        $city = $repository->ofRegionAndTypeAndName($region,'city', 'Moscow');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moscow', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
    }
}