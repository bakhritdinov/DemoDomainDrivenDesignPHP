<?php

namespace App\Tests\Fixture\Language;

use App\Core\Domain\Language\Dto\CreateLanguageDto;

class CreateLanguageDtoFixture
{
    public static function getOne(
        string  $name,
        string  $code,
        ?string $logo = null,
        ?bool   $isActive = null,
    ): CreateLanguageDto
    {
        return new CreateLanguageDto($name, $code, $logo, $isActive);
    }
}