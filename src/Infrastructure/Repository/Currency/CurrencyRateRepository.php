<?php

namespace App\Infrastructure\Repository\Currency;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Entity\CurrencyRate;
use App\Core\Domain\Currency\Repository\CurrencyRateRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager
    )
    {
    }

    public function create(CurrencyRate $currencyRate): void
    {
        $this->entityManager->persist($currencyRate);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(CurrencyRate $currencyRate): void
    {
        $this->entityManager->persist($currencyRate);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofCurrencyFrom(Currency $currencyFrom): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CurrencyRate::class, 'currencyRate')
            ->select('currencyRate')
            ->where('currencyRate.currencyFrom = :currencyFrom')
            ->andWhere('currencyRate.expiredAt IS NULL')
            ->setParameter('currencyFrom', $currencyFrom->getId(), 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function ofCurrencyFromAndCurrencyTo(Currency $currencyFrom, Currency $currencyTo): ?CurrencyRate
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CurrencyRate::class, 'currencyRate')
            ->select('currencyRate')
            ->where('currencyRate.currencyFrom = :currencyFrom')
            ->andWhere('currencyRate.currencyTo = :currencyTo')
            ->andWhere('currencyRate.expiredAt IS NULL')
            ->setParameter('currencyFrom', $currencyFrom->getId(), 'uuid')
            ->setParameter('currencyTo', $currencyTo->getId(), 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CurrencyRate::class, 'currencyRate')
            ->select('currencyRate')
            ->getQuery()
            ->getResult();
    }

}