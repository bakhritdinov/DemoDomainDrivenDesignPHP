<?php

namespace App\Application\City\Command;

use App\Application\CommandHandler;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Service\CreateCityService;

readonly class CreateCityCommandHandler implements CommandHandler
{
    public function __construct(public CreateCityService $createCityService)
    {
    }

    public function __invoke(CreateCityCommand $command): City
    {
        return $this->createCityService->create($command->getCreateCityDto());
    }
}