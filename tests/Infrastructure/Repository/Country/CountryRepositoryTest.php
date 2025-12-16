<?php

namespace App\Tests\Infrastructure\Repository\Country;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Infrastructure\Repository\Country\CountryRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\Fixture\Country\CountryFixture;

class CountryRepositoryTest extends DoctrineTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateCountry()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');

        $repositoryCountry->create($newCountry);

        $country = $repositoryCountry->ofId($newCountry->getId());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getNumericCode(), $country->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $country->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $country->getAlpha3());
    }

    public function testUpdateCountry()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $repositoryCountry->create($newCountry);

        $this->assertEquals('test country', $newCountry->getName());

        $country = $repositoryCountry->ofId($newCountry->getId());

        $country->changeName('updated test country');
        $repositoryCountry->update($country);

        $updatedCountry = $repositoryCountry->ofId($newCountry->getId());

        $this->assertNotNull($updatedCountry->getUpdatedAt());
        $this->assertEquals('updated test country', $updatedCountry->getName());

        $this->assertTrue($updatedCountry->isActive());

        $updatedCountry->changeIsActive(false);
        $repositoryCountry->update($updatedCountry);

        $deactivatedCountry = $repositoryCountry->ofIdDeactivated($updatedCountry->getId());

        $this->assertNotNull($deactivatedCountry);
        $this->assertFalse($deactivatedCountry->isActive());
    }

    public function testOfId()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $repositoryCountry->create($newCountry);

        $country = $repositoryCountry->ofId($newCountry->getId());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getNumericCode(), $country->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $country->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

    public function testOfNumericCode()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $repositoryCountry->create($newCountry);

        $country = $repositoryCountry->ofNumericCode($newCountry->getNumericCode());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getNumericCode(), $country->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $country->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

    public function testOfAlpha2()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $repositoryCountry->create($newCountry);

        $country = $repositoryCountry->ofAlpha2($newCountry->getAlpha2());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getNumericCode(), $country->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $country->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

    public function testOfAlpha3()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $repositoryCountry->create($newCountry);

        $country = $repositoryCountry->ofAlpha3($newCountry->getAlpha3());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getNumericCode(), $country->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $country->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

    public function testOfName()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $repositoryCountry->create($newCountry);

        $country = $repositoryCountry->ofName($newCountry->getName());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getNumericCode(), $country->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $country->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $country->getAlpha3());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

    public function testOfIdDeactivated()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $newCountry->changeIsActive(false);
        $repositoryCountry->create($newCountry);

        $deactivatedCountry = $repositoryCountry->ofIdDeactivated($newCountry->getId());

        $this->assertNotNull($deactivatedCountry);
        $this->assertInstanceOf(Country::class, $deactivatedCountry);
        $this->assertEquals($newCountry->getId(), $deactivatedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deactivatedCountry->getName());
        $this->assertEquals($newCountry->getNumericCode(), $deactivatedCountry->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $deactivatedCountry->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $deactivatedCountry->getAlpha3());
        $this->assertFalse($deactivatedCountry->isActive());
        $this->assertNotNull($deactivatedCountry->getCreatedAt());
        $this->assertNotNull($deactivatedCountry->getUpdatedAt());
    }

    public function testOfAlpha2Deactivated()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $newCountry->changeIsActive(false);
        $repositoryCountry->create($newCountry);

        $deactivatedCountry = $repositoryCountry->ofAlpha2Deactivated($newCountry->getAlpha2());

        $this->assertNotNull($deactivatedCountry);
        $this->assertInstanceOf(Country::class, $deactivatedCountry);
        $this->assertEquals($newCountry->getId(), $deactivatedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deactivatedCountry->getName());
        $this->assertEquals($newCountry->getNumericCode(), $deactivatedCountry->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $deactivatedCountry->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $deactivatedCountry->getAlpha3());
        $this->assertFalse($deactivatedCountry->isActive());
        $this->assertNotNull($deactivatedCountry->getCreatedAt());
        $this->assertNotNull($deactivatedCountry->getUpdatedAt());
    }

    public function testOfNumericCodeDeactivated()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $newCountry->changeIsActive(false);
        $repositoryCountry->create($newCountry);

        $deactivatedCountry = $repositoryCountry->ofNumericCodeDeactivated($newCountry->getNumericCode());

        $this->assertNotNull($deactivatedCountry);
        $this->assertInstanceOf(Country::class, $deactivatedCountry);
        $this->assertEquals($newCountry->getId(), $deactivatedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deactivatedCountry->getName());
        $this->assertEquals($newCountry->getNumericCode(), $deactivatedCountry->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $deactivatedCountry->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $deactivatedCountry->getAlpha3());
        $this->assertFalse($deactivatedCountry->isActive());
        $this->assertNotNull($deactivatedCountry->getCreatedAt());
        $this->assertNotNull($deactivatedCountry->getUpdatedAt());
    }

    public function testOfAlpha3Deactivated()
    {
        $container = $this->getContainer();

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockFinder([]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $newCountry->changeIsActive(false);
        $repositoryCountry->create($newCountry);

        $deactivatedCountry = $repositoryCountry->ofAlpha3Deactivated($newCountry->getAlpha3());

        $this->assertNotNull($deactivatedCountry);
        $this->assertInstanceOf(Country::class, $deactivatedCountry);
        $this->assertEquals($newCountry->getId(), $deactivatedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deactivatedCountry->getName());
        $this->assertEquals($newCountry->getNumericCode(), $deactivatedCountry->getNumericCode());
        $this->assertEquals($newCountry->getAlpha2(), $deactivatedCountry->getAlpha2());
        $this->assertEquals($newCountry->getAlpha3(), $deactivatedCountry->getAlpha3());
        $this->assertFalse($deactivatedCountry->isActive());
        $this->assertNotNull($deactivatedCountry->getCreatedAt());
        $this->assertNotNull($deactivatedCountry->getUpdatedAt());
    }

    public function testPaginate()
    {

        $container = $this->getContainer();

        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');
        $newCountry2 = CountryFixture::getOne('test country2', 363, 'KZ', 'KZZ');

        $array = [$newCountry, $newCountry2];

        $repositoryCountry = new CountryRepository($this->entityManager, $this->getMockPaginate($array), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryCountry);

        $countries = $repositoryCountry->paginate(1, 2);

        $this->assertNotEmpty($countries);
        $this->assertIsArray($countries);
        $this->assertArrayHasKey('data', $countries);
        $this->assertArrayHasKey('total', $countries);
        $this->assertArrayHasKey('pages', $countries);

        $country = $countries['data'][0];

        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());

        $this->assertEquals(2, $countries['total']);
        $this->assertEquals(1, $countries['pages']);
    }

    public function testSearch()
    {
        $newCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS');

        $container = $this->getContainer();
        $repositoryMock = new CountryRepository($this->entityManager, $this->getMockFinder([$newCountry]), $this->getMockPersister());
        $container->set(CountryRepositoryInterface::class, $repositoryMock);

        $this->assertEmpty($repositoryMock->all());

        $repositoryMock->create($newCountry);

        $countries = $repositoryMock->search('RU');

        $this->assertNotEmpty($countries);
        $this->assertIsArray($countries);

        $country = reset($countries);

        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals(643, $country->getNumericCode());
        $this->assertEquals('RU', $country->getAlpha2());
        $this->assertEquals('RUS', $country->getAlpha3());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

}