<?php

namespace App\Tests\Fixture\Language;

use App\Core\Domain\Language\Dto\UpdateLanguageDto;

class UpdateLanguageDtoFixture
{
    public static function getOne(
        ?string $name,
        ?string $logo = null,
        ?bool   $isActive = null,
    ): UpdateLanguageDto
    {
        return new UpdateLanguageDto($name, $logo, $isActive);
    }
}