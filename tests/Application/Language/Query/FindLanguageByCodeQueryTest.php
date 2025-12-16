<?php

namespace App\Tests\Application\Language\Query;

use App\Application\Language\Query\FindLanguageByCodeQuery;
use App\Application\Language\Query\FindLanguageByCodeQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\MessageBusTestCase;

class FindLanguageByCodeQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindLanguageByCodeQuery('ru')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindLanguageByCodeQueryHandler::class)
        );
    }

    public function testFindLanguageByCodeQueryHandler()
    {
        $container = $this->getContainer();
        $newLanguage = LanguageFixture::getOne('Russian', 'ru');

        $this->entityManager->persist($newLanguage);
        $this->entityManager->flush();


        $language = $container->get(FindLanguageByCodeQueryHandler::class)(
            new FindLanguageByCodeQuery($newLanguage->getCode())
        );

        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals('Russian', $language->getName());
        $this->assertEquals('ru', $language->getCode());
        $this->assertNull($language->getLogo());
        $this->assertNotNull($language->getCreatedAt());
        $this->assertNull($language->getUpdatedAt());
    }
}