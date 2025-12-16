<?php

namespace App\Application\Currency\Command;

use App\Application\CommandHandler;
use App\Core\Domain\Currency\Service\UpdateCurrencyService;

readonly class UpdateCurrencyCommandHandler implements CommandHandler
{
    public function __construct(public UpdateCurrencyService $updateCurrencyService)
    {
    }

    public function __invoke(UpdateCurrencyCommand $command): void
    {
        $this->updateCurrencyService->update($command->getCurrencyId(), $command->getUpdateCurrencyDto());
    }
}