<?php

namespace App\Infrastructure\Repository\City;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Region\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface as ElasticSearchFinder;
use FOS\ElasticaBundle\Persister\ObjectPersister as ElasticSearchPersist;
use Symfony\Component\Uid\Uuid;

final class CityRepository implements CityRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ElasticSearchFinder    $finder,
        public ElasticSearchPersist   $persister
    )
    {
    }

    public function create(City $city): City
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($city);
            $this->entityManager->flush();
            $this->persister->insertOne($city);

            $this->entityManager->getConnection()->commit();
            $this->entityManager->clear();

            return $city;
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            $this->entityManager->clear();
            throw $e;
        }
    }

    public function update(City $city): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($city);
            $this->entityManager->flush();
            $this->persister->insertOne($city);

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
            ->from(City::class, 'city')
            ->select('city')
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

        $cities = $this->finder->findPaginated($boolQuery)
            ->setCurrentPage($page)
            ->setMaxPerPage($offset);

        return [
            'data' => $cities->getIterator(),
            'total' => $cities->getNbResults(),
            'pages' => $cities->getNbPages()
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
                        ->addShould((new MatchQuery)->setFieldQuery('type', $query))
                )
        );
    }

    public function ofId(Uuid $cityId): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.id = :id')
            ->andWhere('city.isActive = true')
            ->setParameter('id', $cityId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofRegionPaginate(Region $region, int $page, int $offset, array $filters = ['isActive' => true]): array
    {
        $boolQuery = (new BoolQuery)->addMust((new MatchQuery)->setFieldQuery('region', $region->getId()));

        foreach ($filters as $key => $value) {
            $boolQuery->addFilter(
                (new Term)
                    ->setTerm($key, $value)
            );
        }

        $cities = $this->finder->findPaginated($boolQuery)
            ->setCurrentPage($page)
            ->setMaxPerPage($offset);

        return [
            'data' => $cities->getIterator(),
            'total' => $cities->getNbResults(),
            'pages' => $cities->getNbPages()
        ];
    }

    public function ofIdDeactivated(Uuid $cityId): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.id = :id')
            ->andWhere('city.isActive = false')
            ->setParameter('id', $cityId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofRegionAndTypeAndName(Region $region, string $type, string $name): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.type = :type')
            ->andWhere('city.name = :name')
            ->andWhere('city.region = :regionId')
            ->andWhere('city.isActive = true')
            ->setParameter('type', $type)
            ->setParameter('name', $name)
            ->setParameter('regionId', $region->getId(), 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofRegionAndTypeAndNameDeactivated(Region $region, string $type, string $name): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.type = :type')
            ->andWhere('city.name = :name')
            ->andWhere('city.region = :regionId')
            ->andWhere('city.isActive = false')
            ->setParameter('type', $type)
            ->setParameter('name', $name)
            ->setParameter('regionId', $region->getId(), 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}