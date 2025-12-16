<?php

namespace App\Application\Currency\Command;

use App\Application\CommandHandler;
use App\Core\Domain\Currency\Service\CreateCurrencyRateService;

readonly class CreateCurrencyRateCommandHandler implements CommandHandler
{
    public function __construct(public CreateCurrencyRateService $createCurrencyRateService)
    {
    }

    public function __invoke(CreateCurrencyRateCommand $command): void
    {
        $this->createCurrencyRateService->create($command->getCreateCurrencyRateDto());
    }
}