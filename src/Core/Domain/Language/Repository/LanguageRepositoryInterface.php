<?php

namespace App\Core\Domain\Language\Repository;

use App\Core\Domain\Language\Entity\Language;
use Symfony\Component\Uid\Uuid;

interface LanguageRepositoryInterface
{
    public function create(Language $language): Language;

    public function update(Language $language): void;

    public function all(): array;

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function ofCode(string $code): ?Language;

    public function ofCodeDeactivated(string $code): ?Language;

    public function ofId(Uuid $languageId): ?Language;

    public function ofIdDeactivated(Uuid $languageId): ?Language;
}