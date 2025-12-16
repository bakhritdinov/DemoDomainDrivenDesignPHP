<?php

namespace App\Tests\Application\Language\Query;

use App\Application\Language\Query\FindLanguageByCodeDeactivatedQuery;
use App\Application\Language\Query\FindLanguageByCodeDeactivatedQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\MessageBusTestCase;

class FindLanguageByCodeDeactivatedQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindLanguageByCodeDeactivatedQuery('ru')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindLanguageByCodeDeactivatedQueryHandler::class)
        );
    }

    public function testFindLanguageByCodeDeactivatedQueryHandler()
    {
        $container = $this->getContainer();
        $newLanguage = LanguageFixture::getOne('Russian', 'ru', isActive: false);

        $this->entityManager->persist($newLanguage);
        $this->entityManager->flush();


        $language = $container->get(FindLanguageByCodeDeactivatedQueryHandler::class)(
            new FindLanguageByCodeDeactivatedQuery($newLanguage->getCode())
        );

        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals('Russian', $language->getName());
        $this->assertEquals('ru', $language->getCode());
        $this->assertNull($language->getLogo());
        $this->assertFalse($language->isActive());
        $this->assertNotNull($language->getCreatedAt());
        $this->assertNotNull($language->getUpdatedAt());
    }
}