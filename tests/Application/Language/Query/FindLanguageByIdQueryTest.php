<?php

namespace App\Tests\Application\Language\Query;

use App\Application\Language\Query\FindLanguageByIdQuery;
use App\Application\Language\Query\FindLanguageByIdQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindLanguageByIdQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindLanguageByIdQuery(Uuid::v4())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindLanguageByIdQueryHandler::class)
        );
    }

    public function testFindLanguageByIdQueryHandler()
    {
        $container = $this->getContainer();
        $newLanguage = LanguageFixture::getOne('Russian', 'ru');

        $this->entityManager->persist($newLanguage);
        $this->entityManager->flush();


        $language = $container->get(FindLanguageByIdQueryHandler::class)(
            new FindLanguageByIdQuery($newLanguage->getId())
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