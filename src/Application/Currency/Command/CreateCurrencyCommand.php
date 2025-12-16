<?php

namespace App\Application\Currency\Command;

use App\Application\Command;
use App\Core\Domain\Currency\Dto\CreateCurrencyDto;

readonly class CreateCurrencyCommand implements Command
{
    public function __construct(private CreateCurrencyDto $createCurrencyDto)
    {
    }

    public function getCreateCurrencyDto(): CreateCurrencyDto
    {
        return $this->createCurrencyDto;
    }

}