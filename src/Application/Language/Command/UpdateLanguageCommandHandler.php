<?php

namespace App\Application\Language\Command;

use App\Application\CommandHandler;
use App\Core\Domain\Language\Service\UpdateLanguageService;

class UpdateLanguageCommandHandler implements CommandHandler
{
    public function __construct(public UpdateLanguageService $service)
    {
    }

    public function __invoke(UpdateLanguageCommand $command): void
    {
        $this->service->update($command->getLanguageId(), $command->getUpdateLanguageDto());
    }
}