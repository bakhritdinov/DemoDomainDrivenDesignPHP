<?php

namespace App\Infrastructure\Repository\Language;

use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class LanguageRepository implements LanguageRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Language $language): Language
    {
        $this->entityManager->persist($language);

        $this->entityManager->flush();
        $this->entityManager->clear();

        return $language;
    }

    public function update(Language $language): void
    {
        $this->entityManager->persist($language);

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Language::class, 'language')
            ->select('language')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        foreach ($filters as $key => $value) {
            $queryBuilder
                ->where("language.{$key} = :{$key}")
                ->setParameter($key, $value);
        }

        $query = $queryBuilder
            ->from(Language::class, 'language')
            ->select('language')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int)ceil($total / $offset);

        $languages = $paginator
            ->getQuery()
            ->setFirstResult($offset * ($page - 1))
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $languages,
            'total' => $total,
            'pages' => $pages
        ];
    }

    public function ofCode(string $code): ?Language
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Language::class, 'language')
            ->select('language')
            ->where('language.code = :code')
            ->andWhere('language.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?Language
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Language::class, 'language')
            ->select('language')
            ->where('language.code = :code')
            ->andWhere('language.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofId(Uuid $languageId): ?Language
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Language::class, 'language')
            ->select('language')
            ->where('language.id = :id')
            ->andWhere('language.isActive = true')
            ->setParameter('id', $languageId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $languageId): ?Language
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Language::class, 'language')
            ->select('language')
            ->where('language.id = :id')
            ->andWhere('language.isActive = false')
            ->setParameter('id', $languageId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}