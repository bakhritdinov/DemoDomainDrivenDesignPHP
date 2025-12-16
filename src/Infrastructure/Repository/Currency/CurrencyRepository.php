<?php

namespace App\Infrastructure\Repository\Currency;

use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

final class CurrencyRepository implements CurrencyRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager
    )
    {
    }

    public function create(Currency $currency): Currency
    {
        $this->entityManager->persist($currency);
        $this->entityManager->flush();
        $this->entityManager->clear();
        return $currency;
    }

    public function update(Currency $currency): void
    {
        $this->entityManager->persist($currency);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofId(Uuid $currencyId): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.id = :id')
            ->andWhere('currency.isActive = true')
            ->setParameter('id', $currencyId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $currencyId): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.id = :id')
            ->andWhere('currency.isActive = false')
            ->setParameter('id', $currencyId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $code): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.code = :code')
            ->andWhere('currency.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.code = :code')
            ->andWhere('currency.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNum(int $num): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.num = :num')
            ->andWhere('currency.isActive = true')
            ->setParameter('num', $num)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNumDeactivated(int $num): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.num = :num')
            ->andWhere('currency.isActive = false')
            ->setParameter('num', $num)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        foreach ($filters as $key => $value) {
            $queryBuilder
                ->where("currency.{$key} = :{$key}")
                ->setParameter($key, $value);
        }

        $query = $queryBuilder
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int)ceil($total / $offset);

        $currencies = $paginator
            ->getQuery()
            ->setFirstResult($offset * ($page - 1))
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $currencies,
            'total' => $total,
            'pages' => $pages
        ];
    }
}