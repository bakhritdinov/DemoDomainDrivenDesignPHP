<?php

namespace App\Tests\Core\Domain\Country\Service;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Exception\CountryNotFoundException;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Country\Service\UpdateCountryService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Country\UpdateCountryDtoFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCountryServiceTest extends KernelTestCase
{
    private CountryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(CountryRepositoryInterface::class);
    }

    public function testUpdateCountryName()
    {
        $oldCountry = CountryFixture::getOne();
        $this->repository->method('ofId')->willReturn($oldCountry);

        $service = new UpdateCountryService($this->repository);

        $updateCountryDto = UpdateCountryDtoFixture::getOne(name:'test new country');

        $service->update($oldCountry->getId(), $updateCountryDto);

        $this->repository->method('ofId')->willReturn(CountryFixture::getOne(
            'test new country', 643, 'RU', 'RUS', null, $oldCountry->getId()
        ));

        $newCountry = $this->repository->ofId($oldCountry->getId());

        $this->assertNotNull($newCountry);
        $this->assertInstanceOf(Country::class, $newCountry);
        $this->assertEquals('test new country', $newCountry->getName());
        $this->assertEquals(643, $newCountry->getNumericCode());
        $this->assertEquals('RU', $newCountry->getAlpha2());
        $this->assertEquals('RUS', $newCountry->getAlpha3());
        $this->assertTrue($newCountry->isActive());
        $this->assertNotNull($newCountry->getCreatedAt());
        $this->assertNotNull($newCountry->getUpdatedAt());
    }

    public function testUpdateCountryNameIfNotFound()
    {
        $this->repository->method('ofId')->willReturn(null);

        $service = new UpdateCountryService($this->repository);

        $this->expectException(CountryNotFoundException::class);
        $service->update(Uuid::v1(), UpdateCountryDtoFixture::getOne(name:'test new country'));
    }

    public function testUpdateCountryIsActive()
    {
        $oldCountry = CountryFixture::getOne('test country', 643, 'RU', 'RUS', true);
        $this->repository->method('ofId')->willReturn($oldCountry);

        $service = new UpdateCountryService($this->repository);

        $this->assertTrue($oldCountry->isActive());

        $updateCountryDto = UpdateCountryDtoFixture::getOne(isActive: false);
        $service->update($oldCountry->getId(), $updateCountryDto);

        $this->repository->method('ofId')->willReturn(CountryFixture::getOne(
            'test country', 643, 'RU', 'RUS', false, $oldCountry->getId()
        ));

        $newCountry = $this->repository->ofId($oldCountry->getId());

        $this->assertNotNull($newCountry);
        $this->assertInstanceOf(Country::class, $newCountry);
        $this->assertEquals('test country', $newCountry->getName());
        $this->assertEquals(643, $newCountry->getNumericCode());
        $this->assertEquals('RU', $newCountry->getAlpha2());
        $this->assertEquals('RUS', $newCountry->getAlpha3());
        $this->assertFalse($newCountry->isActive());
        $this->assertNotNull($newCountry->getCreatedAt());
        $this->assertNotNull($newCountry->getUpdatedAt());
    }

    public function testUpdateCountryIsActiveIfNotFound()
    {
        $service = new UpdateCountryService($this->repository);

        $this->expectException(CountryNotFoundException::class);
        $service->update(Uuid::v1(), UpdateCountryDtoFixture::getOne(isActive: false));
    }
}