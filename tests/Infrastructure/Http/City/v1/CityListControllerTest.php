<?php

namespace App\Tests\Infrastructure\Http\City\v1;

use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\City\CityRepository;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class CityListControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testPaginate()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();

        $region = RegionFixture::getOne($country);

        $newCity = CityFixture::getOne($region);
        $newCity2 = CityFixture::getOne($region, 'city', 'Moscow2');

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockPaginate([$newCity, $newCity2]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);

        $this->client
            ->request(
            'GET',
            '/api/v1/city/paginate?page=1&offset=2'
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('type', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('Moscow', $response);
        $this->assertStringContainsString('RU-MOW', $response);
    }
    public function testSearchRoute()
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

        $newCity = CityFixture::getOne($region);
        $repositoryCity = new CityRepository($this->entityManager, $this->getMockFinder([$newCity]), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);


        $this->client
            ->request(
            'GET',
            "/api/v1/city/search?query=Moscow"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

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
    }



    public function testFindByRegion()
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

        $newCity = CityFixture::getOne($region);
        $newCity2 = CityFixture::getOne($region, 'city', 'Moscow2');

        $array = [$newCity, $newCity2];

        $repositoryCity = new CityRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(CityRepositoryInterface::class, $repositoryCity);

        $this->client
            ->request(
            'GET',
            "/api/v1/city/find-by-region-id-paginate?regionId={$region->getId()}&page=1&offset=2"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

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
    }

}