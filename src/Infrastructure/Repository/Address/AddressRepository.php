<?php

namespace App\Infrastructure\Repository\Address;

use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Region\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface as ElasticSearchFinder;
use FOS\ElasticaBundle\Persister\ObjectPersister as ElasticSearchPersist;
use Symfony\Component\Uid\Uuid;

final class AddressRepository implements AddressRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ElasticSearchFinder    $finder,
        public ElasticSearchPersist   $persister
    )
    {
    }

    public function create(Address $address): Address
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            $this->persister->insertOne($address);
            $this->entityManager->getConnection()->commit();
            $this->entityManager->clear();
            return $address;
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            $this->entityManager->clear();
            throw $e;
        }
    }

    public function update(Address $address): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            $this->persister->insertOne($address);
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
            ->from(Address::class, 'address')
            ->select('address')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array
    {
        $addresses = $this->finder->findPaginated(
            (new BoolQuery)
                ->addFilter(
                    (new Term)
                        ->setTerm('isActive', true)
                )
        )
            ->setCurrentPage($page)
            ->setMaxPerPage($offset);

        return [
            'data' => $addresses->getIterator(),
            'total' => $addresses->getNbResults(),
            'pages' => $addresses->getNbPages()
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
                        ->addShould((new MatchQuery)->setFieldQuery('address', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('postalCode', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('street', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('house', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('flat', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('entrance', $query))
                        ->addShould((new MatchQuery)->setFieldQuery('floor', $query))
                )
        );
    }

    public function ofId(Uuid $addressId): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->innerJoin(City::class, 'city', Join::WITH, 'address.city = city')
            ->innerJoin(Region::class, 'region', Join::WITH, 'city.region = region')
            ->innerJoin(Country::class, 'country', Join::WITH, 'region.country = country')
            ->where('address.id = :id')
            ->andWhere('address.isActive = true')
            ->andWhere('city.isActive = true')
            ->andWhere('region.isActive = true')
            ->andWhere('country.isActive = true')
            ->setParameter('id', $addressId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $addressId): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->where('address.id = :id')
            ->andWhere('address.isActive = false')
            ->setParameter('id', $addressId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCityPaginate(City $city, int $page, int $offset, array $filters = ['isActive' => true]): array
    {
        $boolQuery = (new BoolQuery)->addMust((new MatchQuery)->setFieldQuery('city', $city->getId()));

        foreach ($filters as $key => $value) {
            $boolQuery->addFilter(
                (new Term)
                    ->setTerm($key, $value)
            );
        }

        $addresses = $this->finder->findPaginated($boolQuery)
            ->setCurrentPage($page)
            ->setMaxPerPage($offset);

        return [
            'data' => $addresses->getIterator(),
            'total' => $addresses->getNbResults(),
            'pages' => $addresses->getNbPages()
        ];
    }

    public function ofAddress(string $address): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->innerJoin(City::class, 'city', Join::WITH, 'address.city = city')
            ->innerJoin(Region::class, 'region', Join::WITH, 'city.region = region')
            ->innerJoin(Country::class, 'country', Join::WITH, 'region.country = country')
            ->where('address.address = :address')
            ->andWhere('address.isActive = true')
            ->andWhere('city.isActive = true')
            ->andWhere('region.isActive = true')
            ->andWhere('country.isActive = true')
            ->setParameter('address', $address)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofAddressDeactivated(string $address): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->where('address.address = :address')
            ->andWhere('address.isActive = false')
            ->setParameter('address', $address)
            ->getQuery()
            ->getOneOrNullResult();
    }
}