<?php

namespace App\Infrastructure\Repository\Country;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface as ElasticSearchFinder;
use FOS\ElasticaBundle\Persister\ObjectPersister as ElasticSearchPersist;
use Symfony\Component\Uid\Uuid;

final class CountryRepository implements CountryRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ElasticSearchFinder    $finder,
        public ElasticSearchPersist   $persister
    )
    {
    }

    public function create(Country $country): Country
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($country);
            $this->entityManager->flush();
            $this->persister->insertOne($country);

            $this->entityManager->getConnection()->commit();
            $this->entityManager->clear();

            return $country;
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            $this->entityManager->clear();
            throw $e;
        }
    }

    public function update(Country $country): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($country);
            $this->entityManager->flush();
            $this->persister->insertOne($country);

            $this->entityManager->getConnection()->commit();

        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array
    {
        $boolQuery = (new BoolQuery);

        foreach ($filters as $key => $value) {
            $boolQuery->addFilter(
                (new Term)
                    ->setTerm($key, $value)
            );
        }

        $countries = $this->finder->findPaginated($boolQuery)
            ->setCurrentPage($page)
            ->setMaxPerPage($offset);

        return [
            'data' => $countries->getIterator(),
            'total' => $countries->getNbResults(),
            'pages' => $countries->getNbPages()
        ];
    }

    public function search(string $query, array $filters = ['isActive' => true]): array
    {
        $boolQuery = (new BoolQuery);

        foreach ($filters as $key => $value) {
            $boolQuery->addFilter(
                (new Term)
                    ->setTerm($key, $value)
            );
        }

        return $this->finder->find(
            $boolQuery
                ->addShould(
                    (new BoolQuery)
                        ->addShould((new MatchQuery)->setFieldQuery('name', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('numericCode', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('alpha2', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('alpha3', $query))
                )
        );
    }

    public function ofId(Uuid $countryId): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.id = :id')
            ->andWhere('country.isActive = true')
            ->setParameter('id', $countryId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $countryId): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.id = :id')
            ->andWhere('country.isActive = false')
            ->setParameter('id', $countryId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofName(string $name): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.name = :name')
            ->andWhere('country.isActive = true')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNameDeactivated(string $name): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.name = :name')
            ->andWhere('country.isActive = false')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNumericCode(int $numericCode): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.numericCode = :numericCode')
            ->andWhere('country.isActive = true')
            ->setParameter('numericCode', $numericCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNumericCodeDeactivated(int $numericCode): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.numericCode = :numericCode')
            ->andWhere('country.isActive = false')
            ->setParameter('numericCode', $numericCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofAlpha2(string $alpha2): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.alpha2 = :alpha2')
            ->andWhere('country.isActive = true')
            ->setParameter('alpha2', $alpha2)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofAlpha2Deactivated(string $alpha2): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.alpha2 = :alpha2')
            ->andWhere('country.isActive = false')
            ->setParameter('alpha2', $alpha2)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofAlpha3(string $alpha3): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.alpha3 = :alpha3')
            ->andWhere('country.isActive = true')
            ->setParameter('alpha3', $alpha3)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofAlpha3Deactivated(string $alpha3): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.alpha3 = :alpha3')
            ->andWhere('country.isActive = false')
            ->setParameter('alpha3', $alpha3)
            ->getQuery()
            ->getOneOrNullResult();
    }
}