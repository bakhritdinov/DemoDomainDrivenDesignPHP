<?php

namespace App\Infrastructure\Repository\Region;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface as ElasticSearchFinder;
use FOS\ElasticaBundle\Persister\ObjectPersister as ElasticSearchPersist;
use Symfony\Component\Uid\Uuid;

final class RegionRepository implements RegionRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ElasticSearchFinder    $finder,
        public ElasticSearchPersist   $persister
    )
    {
    }

    public function create(Region $region): Region
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($region);
            $this->entityManager->flush();
            $this->persister->insertOne($region);

            $this->entityManager->getConnection()->commit();
            $this->entityManager->clear();

            return $region;
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            $this->entityManager->clear();
            throw $e;
        }
    }

    public function update(Region $region): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($region);
            $this->entityManager->flush();
            $this->persister->insertOne($region);

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
            ->from(Region::class, 'region')
            ->select('region')
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

        $regions = $this->finder->findPaginated($boolQuery)
            ->setCurrentPage($page)
            ->setMaxPerPage($offset);

        return [
            'data' => $regions->getIterator(),
            'total' => $regions->getNbResults(),
            'pages' => $regions->getNbPages()
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
                        ->addShould((new MatchQuery)->setFieldQuery('code', $query))
                )
        );
    }

    public function ofId(Uuid $regionId): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.id = :id')
            ->andWhere('region.isActive = true')
            ->setParameter('id', $regionId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCountryPaginate(Country $country, int $page, int $offset, array $filters = ['isActive' => true]): array
    {
        $boolQuery = (new BoolQuery)->addMust((new MatchQuery)->setFieldQuery('country', $country->getId()));

        foreach ($filters as $key => $value) {
            $boolQuery->addFilter(
                (new Term)->setTerm($key, $value)
            );
        }

        $regions = $this->finder->findPaginated($boolQuery)
            ->setCurrentPage($page)
            ->setMaxPerPage($offset);

        return [
            'data' => $regions->getIterator(),
            'total' => $regions->getNbResults(),
            'pages' => $regions->getNbPages()
        ];
    }

    public function ofIdDeactivated(Uuid $regionId): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.id = :id')
            ->andWhere('region.isActive = false')
            ->setParameter('id', $regionId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $code): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.code = :code')
            ->andWhere('region.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.code = :code')
            ->andWhere('region.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofName(string $name): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.name = :name')
            ->andWhere('region.isActive = true')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNameDeactivated(string $name): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.name = :name')
            ->andWhere('region.isActive = false')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}