<?php

namespace App\Tests\Application\Language\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Language\Command\UpdateLanguageCommand;
use App\Application\Language\Command\UpdateLanguageCommandHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use App\Tests\Fixture\Language\LanguageFixture;
use App\Tests\Fixture\Language\UpdateLanguageDtoFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateLanguageCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateLanguageCommand(Uuid::v4(), UpdateLanguageDtoFixture::getOne('Russian', 'ru'))
        );

        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateLanguageCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateLanguageCommand(Uuid::v4(), UpdateLanguageDtoFixture::getOne('Russian', 'ru'));
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateLanguageCommandHandler()
    {
        $container = $this->getContainer();

        $newLanguage = LanguageFixture::getOne('Russian', 'ru');
        $languageRepository = $container->get(LanguageRepositoryInterface::class);
        $languageRepository->create($newLanguage);

        $updateLanguageDto = UpdateLanguageDtoFixture::getOne('Updated language name');

        $container->get(UpdateLanguageCommandHandler::class)(
            new UpdateLanguageCommand($newLanguage->getId(), $updateLanguageDto)
        );

        $language = $languageRepository->ofCode('ru');

        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals('Updated language name', $language->getName());
        $this->assertEquals('ru', $language->getCode());
        $this->assertNull($language->getLogo());
        $this->assertNotNull($language->getCreatedAt());
        $this->assertNotNull($language->getUpdatedAt());
    }
}