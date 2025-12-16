<?php

namespace App\Tests\Infrastructure\Http\Currency\v1;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Infrastructure\Repository\Currency\CurrencyRepository;
use App\Tests\ElasticSearchMockTrait;
use App\Tests\HttpTestCase;

class CreateCurrencyControllerTest extends HttpTestCase
{
    use ElasticSearchMockTrait;

    public function testCreateCurrencyRoute()
    {
        $container = $this->getContainer();
        $repository = new CurrencyRepository($this->entityManager);
        $container->set(CurrencyRepositoryInterface::class, $repository);

        $this->client
            ->request(
            'POST',
            '/api/v1/currency',
            [
                'code' => 'RUB',
                'name' => 'Russian ruble',
                'num' => 810
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);

        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);

        $currency = $repository->ofCode('RUB');

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }
}