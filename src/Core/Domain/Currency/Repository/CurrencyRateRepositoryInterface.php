<?php

namespace App\Core\Domain\Currency\Repository;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Entity\CurrencyRate;

interface CurrencyRateRepositoryInterface
{
    public function create(CurrencyRate $currencyRate): void;

    public function update(CurrencyRate $currencyRate): void;

    public function ofCurrencyFrom(Currency $currencyFrom): array;

    public function all(): array;

    public function ofCurrencyFromAndCurrencyTo(Currency $currencyFrom, Currency $currencyTo): ?CurrencyRate;
}