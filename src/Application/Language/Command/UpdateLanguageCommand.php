<?php

namespace App\Application\Language\Command;

use App\Application\Command;
use App\Core\Domain\Language\Dto\UpdateLanguageDto;
use Symfony\Component\Uid\Uuid;

readonly class UpdateLanguageCommand implements Command
{
    public function __construct(private Uuid $languageId, private UpdateLanguageDto $updateLanguageDto)
    {
    }

    public function getLanguageId(): Uuid
    {
        return $this->languageId;
    }

    public function getUpdateLanguageDto(): UpdateLanguageDto
    {
        return $this->updateLanguageDto;
    }
}