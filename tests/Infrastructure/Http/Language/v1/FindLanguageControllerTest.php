<?php

namespace App\Tests\Infrastructure\Http\Language\v1;

use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\HttpTestCase;

class FindLanguageControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $container = $this->getContainer();
        $repositoryLanguage = $container->get(LanguageRepositoryInterface::class);

        $language = LanguageFixture::getOne('Russian', 'ru');
        $repositoryLanguage->create($language);

        $this->client
            ->request(
                'GET',
                "/api/v1/language/find-by-id/{$language->getId()->toRfc4122()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('logo', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('Russian', $response);
        $this->assertStringContainsString('ru', $response);
    }

    public function testFindByCode()
    {
        $container = $this->getContainer();
        $repositoryLanguage = $container->get(LanguageRepositoryInterface::class);

        $language = LanguageFixture::getOne('Russian', 'ru');
        $repositoryLanguage->create($language);

        $this->client
            ->request(
                'GET',
                "/api/v1/language/find-by-code/{$language->getCode()}"
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('logo', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('Russian', $response);
        $this->assertStringContainsString('ru', $response);
    }
}