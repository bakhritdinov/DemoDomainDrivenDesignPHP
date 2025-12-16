<?php

namespace App\Tests\Core\Domain\Currency\Service;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Exception\CurrencyAlreadyCreatedException;
use App\Core\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Core\Domain\Currency\Service\CreateCurrencyService;
use App\Tests\Fixture\Currency\CreateCurrencyDtoFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCurrencyServiceTest extends KernelTestCase
{
    public function testCreateCurrencyService()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);

        $service = new CreateCurrencyService($repositoryMock);

        $createCurrencyDto = CreateCurrencyDtoFixture::getOne('RUB', 810, 'Russian ruble');
        $service->create($createCurrencyDto);

        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOne(
            'RUB', 810, 'Russian ruble'
        ));

        $currency = $repositoryMock->ofCode('RUB');

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testCreateCurrencyAlreadyCreatedService()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOne(
            'RUB', 810, 'Russian ruble'
        ));

        $service = new CreateCurrencyService($repositoryMock);

        $this->expectException(CurrencyAlreadyCreatedException::class);
        $createCurrencyDto = CreateCurrencyDtoFixture::getOne('RUB', 810, 'Russian ruble');
        $service->create($createCurrencyDto);
    }

    public function testCreateCurrencyDeactivatedService()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $repositoryMock->method('ofCodeDeactivated')->willReturn(CurrencyFixture::getOneDeactivated(
            'RUB', 810, 'Russian ruble'
        ));

        $service = new CreateCurrencyService($repositoryMock);

        $this->expectException(CurrencyDeactivatedException::class);
        $createCurrencyDto = CreateCurrencyDtoFixture::getOne('RUB', 810, 'Russian ruble');
        $service->create($createCurrencyDto);
    }
}