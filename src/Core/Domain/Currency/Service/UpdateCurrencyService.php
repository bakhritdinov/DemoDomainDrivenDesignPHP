<?php

namespace App\Core\Domain\Currency\Service;

use App\Core\Domain\Currency\Dto\UpdateCurrencyDto;
use App\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class UpdateCurrencyService
{
    public function __construct(
        public CurrencyRepositoryInterface $repository
    )
    {
    }

    public function update(Uuid $currencyId, UpdateCurrencyDto $updateCurrencyDto): void
    {
        $currency = $this->repository->ofId($currencyId);

        if (is_null($currency)) {
            $currency = $this->repository->ofIdDeactivated($currencyId);
            if (is_null($currency)) {
                throw new CurrencyNotFoundException(sprintf('Currency with id %s not found', $currencyId->toRfc4122()));
            }
        }

        if (!is_null($updateCurrencyDto->name)) {
            $currency->changeName($updateCurrencyDto->name);
        }

        if (!is_null($updateCurrencyDto->num)) {
            $currency->changeName($updateCurrencyDto->num);
        }

        if (!is_null($updateCurrencyDto->code)) {
            $currency->changeName($updateCurrencyDto->code);
        }

        if (!is_null($updateCurrencyDto->isActive) && !$currency->equalsIsActive($updateCurrencyDto->isActive)) {
            $currency->changeIsActive($updateCurrencyDto->isActive);
        }

        $this->repository->update($currency);
    }
}