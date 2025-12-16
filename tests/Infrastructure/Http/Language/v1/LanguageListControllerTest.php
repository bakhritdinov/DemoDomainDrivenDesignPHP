<?php

namespace App\Tests\Infrastructure\Http\Language\v1;

use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\HttpTestCase;

class LanguageListControllerTest extends HttpTestCase
{
    public function testPaginate()
    {
        $container = $this->getContainer();

        $newLanguage = LanguageFixture::getOne('Russian', 'ru');
        $newLanguage2 = LanguageFixture::getOne('English', 'en');

        $repositoryLanguage = $container->get(LanguageRepositoryInterface::class);
        $repositoryLanguage->create($newLanguage);
        $repositoryLanguage->create($newLanguage2);

        $this->client
            ->request(
                'GET',
                '/api/v1/language/paginate',
                [
                    'page' => 1,
                    'offset' => 2
                ]
            );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('data', $response);
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('logo', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('Russian', $response);
        $this->assertStringContainsString('English', $response);
        $this->assertStringContainsString('ru', $response);
        $this->assertStringContainsString('en', $response);
    }
}