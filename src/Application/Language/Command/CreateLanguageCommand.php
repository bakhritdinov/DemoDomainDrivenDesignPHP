<?php

namespace App\Application\Language\Command;

use App\Application\Command;
use App\Core\Domain\Language\Dto\CreateLanguageDto;

readonly class CreateLanguageCommand implements Command
{
    public function __construct(private CreateLanguageDto $createLanguageDto)
    {
    }

    public function getCreateLanguageDto(): CreateLanguageDto
    {
        return $this->createLanguageDto;
    }
}