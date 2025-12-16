<?php

namespace App\Tests\Core\Domain\Currency\Service;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Core\Domain\Currency\Service\UpdateCurrencyService;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Currency\UpdateCurrencyDtoFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCurrencyServiceTest extends KernelTestCase
{
    public function testUpdateCurrencyName()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repositoryMock->method('ofId')->willReturn(CurrencyFixture::getOne(
            $currency->getCode(), $currency->getNum(), $currency->getName()
        ));

        $service = new UpdateCurrencyService($repositoryMock);

        $updateCurrencyDto = UpdateCurrencyDtoFixture::getOne(null, null, 'Updated russian ruble');

        $service->update($currency->getId(), $updateCurrencyDto);

        $repositoryMock->method('ofId')->willReturn(CurrencyFixture::getOne(
            'RUB', 810, 'Updated russian ruble'
        ));

        $currency = $repositoryMock->ofId($currency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Updated russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }

    public function testUpdateCurrencyIsActive()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repositoryMock->method('ofId')->willReturn(CurrencyFixture::getOne(
            $currency->getCode(), $currency->getNum(), $currency->getName()
        ));

        $service = new UpdateCurrencyService($repositoryMock);

        $updateCurrencyDto = UpdateCurrencyDtoFixture::getOne(null, null, null, false);

        $service->update($currency->getId(), $updateCurrencyDto);

        $repositoryMock->method('ofId')->willReturn(CurrencyFixture::getOne(
            'RUB', 810, 'Updated russian ruble'
        ));

        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOneDeactivated(
            $currency->getCode(), $currency->getNum(), $currency->getName(), $currency->getId()
        ));

        $currency = $repositoryMock->ofId($currency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }
}