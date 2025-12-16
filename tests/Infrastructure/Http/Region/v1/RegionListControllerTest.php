<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Infrastructure\Repository\Region\RegionRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class RegionListControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testPaginate()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne();

        $newRegion = RegionFixture::getOne($country);
        $newRegion2 = RegionFixture::getOne($country, 'Moscow1', 'AMU', true, Uuid::v1());

        $repositoryRegion = new RegionRepository($this->entityManager,  $this->getMockPaginate([$newRegion, $newRegion2]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $this->client
            ->request(
            'GET',
            '/api/v1/region/paginate?page=1&offset=2'
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
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

        $newRegion = RegionFixture::getOne($country);

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockFinder([$newRegion]), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $this->client
            ->request(
            'GET',
            "/api/v1/region/search?query=Moscow"
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

    public function testFindByCountry()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $country = CountryFixture::getOne();
        $repositoryCountry->create($country);
        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country);
        $newRegion2 = RegionFixture::getOne($country, 'Moscow1', 'AMU', true, Uuid::v1());

        $array = [$newRegion, $newRegion2];

        $repositoryRegion = new RegionRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(RegionRepositoryInterface::class, $repositoryRegion);

        $this->client
            ->request(
            'GET',
            "/api/v1/region/find-by-country-id-paginate?countryId={$country->getId()}&page=1&offset=2"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

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
    }
}