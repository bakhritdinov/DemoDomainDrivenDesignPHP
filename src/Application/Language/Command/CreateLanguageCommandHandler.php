<?php

namespace App\Application\Language\Command;

use App\Application\CommandHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Service\CreateLanguageService;

class CreateLanguageCommandHandler implements CommandHandler
{
    public function __construct(public CreateLanguageService $service)
    {
    }

    public function __invoke(CreateLanguageCommand $command): Language
    {
        return $this->service->create($command->getCreateLanguageDto());
    }
}