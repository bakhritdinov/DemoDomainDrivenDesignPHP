<?php

namespace App\Application\Currency\Command;

use App\Application\Command;
use App\Core\Domain\Currency\Dto\UpdateCurrencyDto;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCurrencyCommand implements Command
{
    public function __construct(private Uuid $currencyId, private UpdateCurrencyDto $updateCurrencyDto)
    {
    }

    public function getCurrencyId(): Uuid
    {
        return $this->currencyId;
    }

    public function getUpdateCurrencyDto(): UpdateCurrencyDto
    {
        return $this->updateCurrencyDto;
    }

}