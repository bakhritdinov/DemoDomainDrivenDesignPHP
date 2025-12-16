<?php

namespace App\Application\Currency\Command;

use App\Application\CommandHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Service\CreateCurrencyService;

readonly class CreateCurrencyCommandHandler implements CommandHandler
{
    public function __construct(public CreateCurrencyService $createCurrencyService)
    {
    }

    public function __invoke(CreateCurrencyCommand $command): Currency
    {
        return $this->createCurrencyService->create($command->getCreateCurrencyDto());
    }
}