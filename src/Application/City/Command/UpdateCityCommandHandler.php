<?php

namespace App\Application\City\Command;

use App\Application\CommandHandler;
use App\Core\Domain\City\Service\UpdateCityService;

readonly class UpdateCityCommandHandler implements CommandHandler
{
    public function __construct(public UpdateCityService $updateCityService)
    {
    }

    public function __invoke(UpdateCityCommand $command): void
    {
        $this->updateCityService->update($command->getCityId(), $command->getUpdateCityDto());
    }
}