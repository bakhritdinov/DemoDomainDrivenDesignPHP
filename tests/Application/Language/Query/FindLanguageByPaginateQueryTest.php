<?php

namespace App\Tests\Application\Language\Query;

use App\Application\Language\Query\FindLanguageByPaginateQuery;
use App\Application\Language\Query\FindLanguageByPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\MessageBusTestCase;

class FindLanguageByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindLanguageByPaginateQuery(1, 2)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindLanguageByPaginateQueryHandler::class)
        );
    }

    public function testFindLanguageByPaginateQueryHandler()
    {
        $container = $this->getContainer();
        $repositoryLanguage = $container->get(LanguageRepositoryInterface::class);
        $newLanguage = LanguageFixture::getOne('Russian', 'ru');
        $newLanguage2 = LanguageFixture::getOne('English', 'en');

        $repositoryLanguage->create($newLanguage);
        $repositoryLanguage->create($newLanguage2);

        $languages = $container->get(FindLanguageByPaginateQueryHandler::class)(
            new FindLanguageByPaginateQuery(2, 1)
        );

        $this->assertNotEmpty($languages);
        $this->assertIsArray($languages);

        $this->assertArrayHasKey('data', $languages);
        $this->assertArrayHasKey('total', $languages);
        $this->assertArrayHasKey('pages', $languages);

        $language = reset($languages['data']);

        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals('English', $language->getName());
        $this->assertEquals('en', $language->getCode());
        $this->assertNull($language->getLogo());
        $this->assertNotNull($language->getCreatedAt());
        $this->assertNull($language->getUpdatedAt());
    }
}