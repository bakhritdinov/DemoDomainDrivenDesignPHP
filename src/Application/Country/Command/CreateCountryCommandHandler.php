<?php

namespace App\Application\Country\Command;

use App\Application\CommandHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Service\CreateCountryService;

readonly class CreateCountryCommandHandler implements CommandHandler
{
    public function __construct(public CreateCountryService $createCountryService)
    {
    }

    public function __invoke(CreateCountryCommand $command): Country
    {
        return $this->createCountryService->create($command->getCreateCountryDto());
    }
}