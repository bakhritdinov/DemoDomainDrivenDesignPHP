<?php

namespace App\Application\Country\Command;

use App\Application\Command;
use App\Core\Domain\Country\Dto\UpdateCountryDto;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCountryCommand implements Command
{
    public function __construct(private Uuid $countryId, private UpdateCountryDto $updateCountryDto)
    {
    }

    public function getCountryId(): Uuid
    {
        return $this->countryId;
    }

    public function getUpdateCountryDto(): UpdateCountryDto
    {
        return $this->updateCountryDto;
    }
}