<?php

namespace App\Core\Domain\Language\Service;

use App\Core\Domain\Language\Dto\UpdateLanguageDto;
use App\Core\Domain\Language\Exception\LanguageNotFoundException;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class UpdateLanguageService
{
    public function __construct(public LanguageRepositoryInterface $languageRepository)
    {
    }

    public function update(Uuid $languageId, UpdateLanguageDto $dto): void
    {
        $language = $this->languageRepository->ofId($languageId);

        if (is_null($language)) {
            throw new LanguageNotFoundException(sprintf('Language with id %s not found', $languageId->toRfc4122()));
        }

        if (!is_null($dto->name)) {
            $language->changeName($dto->name);
        }

        if (!is_null($dto->logo)) {
            $language->changeLogo($dto->logo);
        }

        if (!is_null($dto->isActive) && !$language->equalsIsActive($dto->isActive)) {
            $language->changeIsActive($dto->isActive);
        }

        $this->languageRepository->update($language);
    }
}