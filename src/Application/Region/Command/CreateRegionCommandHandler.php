<?php

namespace App\Application\Region\Command;

use App\Application\CommandHandler;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Service\CreateRegionService;

readonly class CreateRegionCommandHandler implements CommandHandler
{
    public function __construct(public CreateRegionService $createRegionService)
    {
    }

    public function __invoke(CreateRegionCommand $command): Region
    {
        return $this->createRegionService->create($command->getCreateRegionDto());
    }
}