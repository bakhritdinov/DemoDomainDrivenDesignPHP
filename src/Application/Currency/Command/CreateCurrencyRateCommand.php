<?php

namespace App\Application\Currency\Command;

use App\Application\Command;
use App\Core\Domain\Currency\Dto\CreateCurrencyRateDto;

readonly class CreateCurrencyRateCommand implements Command
{
    public function __construct(private CreateCurrencyRateDto $createCurrencyRateDto)
    {
    }

    public function getCreateCurrencyRateDto(): CreateCurrencyRateDto
    {
        return $this->createCurrencyRateDto;
    }

}