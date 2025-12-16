<?php

namespace App\Tests\Application\Language\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Language\Command\CreateLanguageCommand;
use App\Application\Language\Command\CreateLanguageCommandHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use App\Tests\Fixture\Language\CreateLanguageDtoFixture;
use App\Tests\MessageBusTestCase;

class CreateLanguageCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateLanguageCommand(CreateLanguageDtoFixture::getOne('Russian', 'ru'))
        );

        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateLanguageCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateLanguageCommand(CreateLanguageDtoFixture::getOne('Russian', 'ru'));
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateLanguageCommandHandler()
    {
        $container = $this->getContainer();

        $createLanguageDto = CreateLanguageDtoFixture::getOne('Russian', 'ru');
        $languageRepository = $container->get(LanguageRepositoryInterface::class);
        $container->get(CreateLanguageCommandHandler::class)(
            new CreateLanguageCommand($createLanguageDto)
        );

        $language = $languageRepository->ofCode('ru');

        $this->assertNotNull($language);
        $this->assertInstanceOf(Language::class, $language);
        $this->assertEquals('Russian', $language->getName());
        $this->assertEquals('ru', $language->getCode());
        $this->assertNull($language->getLogo());
        $this->assertNotNull($language->getCreatedAt());
        $this->assertNull($language->getUpdatedAt());
    }
}