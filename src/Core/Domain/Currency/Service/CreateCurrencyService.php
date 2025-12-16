<?php

namespace App\Core\Domain\Currency\Service;

use App\Core\Domain\Currency\Dto\CreateCurrencyDto;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Exception\CurrencyAlreadyCreatedException;
use App\Core\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;

class CreateCurrencyService
{
    public function __construct(
        public CurrencyRepositoryInterface $currencyRepository
    )
    {
    }

    public function create(CreateCurrencyDto $createCurrencyDto): Currency
    {
        $currency = $this->currencyRepository->ofCode($createCurrencyDto->code);

        if (!is_null($currency)) {
            throw new CurrencyAlreadyCreatedException(sprintf('Currency with code %s already created', $createCurrencyDto->code));
        }

        $currency = $this->currencyRepository->ofCodeDeactivated($createCurrencyDto->code);
        if (!is_null($currency)) {
            throw new CurrencyDeactivatedException(sprintf('Currency with code %s deactivated', $createCurrencyDto->code));
        }

        $currency = new Currency($createCurrencyDto->code, $createCurrencyDto->num, $createCurrencyDto->name);

        if (!is_null($createCurrencyDto->isActive)) {
            $currency->changeIsActive($createCurrencyDto->isActive);
        }

        return $this->currencyRepository->create($currency);
    }
}