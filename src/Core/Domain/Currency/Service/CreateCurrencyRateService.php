<?php

namespace App\Core\Domain\Currency\Service;

use App\Core\Domain\Currency\Dto\CreateCurrencyRateDto;
use App\Core\Domain\Currency\Entity\CurrencyRate;
use App\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Core\Domain\Currency\Repository\CurrencyRateRepositoryInterface;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;

class CreateCurrencyRateService
{
    public function __construct(
        public CurrencyRepositoryInterface     $currencyRepository,
        public CurrencyRateRepositoryInterface $currencyRateRepository
    )
    {
    }

    public function create(CreateCurrencyRateDto $createCurrencyRateDto): void
    {
        $currencyFrom = $this->currencyRepository->ofCode($createCurrencyRateDto->currencyCodeFrom);

        if (is_null($currencyFrom)) {
            $currencyFrom = $this->currencyRepository->ofCodeDeactivated($createCurrencyRateDto->currencyCodeFrom);

            if (is_null($currencyFrom)) {
                throw new CurrencyNotFoundException(sprintf('CurrencyRate from with code %s not found', $createCurrencyRateDto->currencyCodeFrom));
            }
        }

        $currencyTo = $this->currencyRepository->ofCode($createCurrencyRateDto->currencyCodeTo);

        if (is_null($currencyTo)) {
            $currencyTo = $this->currencyRepository->ofCodeDeactivated($createCurrencyRateDto->currencyCodeTo);

            if (is_null($currencyTo)) {
                throw new CurrencyNotFoundException(sprintf('CurrencyRate to with code %s not found', $createCurrencyRateDto->currencyCodeTo));
            }
        }

        $currencyRate = $this->currencyRateRepository->ofCurrencyFromAndCurrencyTo($currencyFrom, $currencyTo);

        if (!is_null($currencyRate)) {
            $currencyRate->expired();
        }

        $currencyRate = new CurrencyRate($currencyFrom, $currencyTo, $createCurrencyRateDto->rate);

        $this->currencyRateRepository->create($currencyRate);
    }
}