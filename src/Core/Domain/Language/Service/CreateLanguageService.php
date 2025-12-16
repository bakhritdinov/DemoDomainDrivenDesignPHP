<?php

namespace App\Core\Domain\Language\Service;

use App\Core\Domain\Language\Dto\CreateLanguageDto;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Exception\LanguageAlreadyCreatedException;
use App\Core\Domain\Language\Exception\LanguageDeactivatedException;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;

class CreateLanguageService
{
    public function __construct(public LanguageRepositoryInterface $languageRepository)
    {
    }

    public function create(CreateLanguageDto $dto): Language
    {
        $language = $this->languageRepository->ofCode($dto->code);

        if (!is_null($language)) {
            throw new LanguageAlreadyCreatedException(sprintf('Language with code %s already created', $dto->code));
        }

        $language = $this->languageRepository->ofCodeDeactivated($dto->code);
        if (!is_null($language)) {
            throw new LanguageDeactivatedException(sprintf('Language with code %s deactivated', $dto->code));
        }

        $language = new Language($dto->name, $dto->code, $dto->logo);

        if (!is_null($dto->isActive)) {
            $language->changeIsActive($dto->isActive);
        }

        return $this->languageRepository->create($language);
    }
}