<?php

namespace App\Tests\Application\Language\Query;

use App\Application\Language\Query\FindLanguageByIdDeactivatedQuery;
use App\Application\Language\Query\FindLanguageByIdDeactivatedQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindLanguageByIdDeactivatedQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindLanguageByIdDeactivatedQuery(Uuid::v4())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindLanguageByIdDeactivatedQueryHandler::class)
        );
    }

    public function testFindLanguageByIdDeactivatedQueryHandler()
    {
        $container = $this->getContainer();
        $newLanguage = LanguageFixture::getOne('Russian', 'ru', isActive: false);

        $this->entityManager->persist($newLanguage);
        $this->entityManager->flush();


        $language = $container->get(FindLanguageByIdDeactivatedQueryHandler::class)(
            new FindLanguageByIdDeactivatedQuery($newLanguage->getId())
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