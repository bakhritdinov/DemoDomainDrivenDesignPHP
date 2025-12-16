<?php

namespace App\Tests\Application\Language\Query;

use App\Application\Language\Query\FindLanguageQuery;
use App\Application\Language\Query\FindLanguageQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\MessageBusTestCase;

class FindLanguageQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindLanguageQuery
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindLanguageQueryHandler::class)
        );
    }

    public function testFindLanguageQueryHandler()
    {
        $container = $this->getContainer();
        $repositoryLanguage = $container->get(LanguageRepositoryInterface::class);
        $newLanguage = LanguageFixture::getOne('Russian', 'ru');
        $newLanguage2 = LanguageFixture::getOne('English', 'en');

        $repositoryLanguage->create($newLanguage);
        $repositoryLanguage->create($newLanguage2);

        $languages = $container->get(FindLanguageQueryHandler::class)(
            new FindLanguageQuery
        );

        $this->assertNotEmpty($languages);
        $this->assertIsArray($languages);

        $language = reset($languages);

        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals('Russian', $language->getName());
        $this->assertEquals('ru', $language->getCode());
        $this->assertNull($language->getLogo());
        $this->assertNotNull($language->getCreatedAt());
        $this->assertNull($language->getUpdatedAt());
    }
}