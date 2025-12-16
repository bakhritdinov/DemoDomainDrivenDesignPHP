<?php

namespace App\Application\Region\Command;

use App\Application\Command;
use App\Core\Domain\Region\Dto\UpdateRegionDto;
use Symfony\Component\Uid\Uuid;

readonly class UpdateRegionCommand implements Command
{
    public function __construct(private Uuid $regionId, private UpdateRegionDto $updateRegionDto)
    {
    }

    public function getRegionId(): Uuid
    {
        return $this->regionId;
    }

    public function getUpdateRegionDto(): UpdateRegionDto
    {
        return $this->updateRegionDto;
    }
}