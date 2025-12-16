<?php

namespace App\Application\Country\Command;

use App\Application\Command;
use App\Core\Domain\Country\Dto\CreateCountryDto;

readonly class CreateCountryCommand implements Command
{
    public function __construct(private CreateCountryDto $createCountryDto)
    {
    }

    public function getCreateCountryDto(): CreateCountryDto
    {
        return $this->createCountryDto;
    }
}