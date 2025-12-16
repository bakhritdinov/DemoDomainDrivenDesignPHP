<?php

namespace App\Tests\Fixture\CommonService\Translatable\Dto;

use App\Core\CommonService\Translatable\Dto\TranslatableFieldDto;

class TranslatableFieldDtoFixture
{
    public static function getOne(string $field, string $locale, ?string $content = null): TranslatableFieldDto
    {
        return new TranslatableFieldDto($field, $locale, $content);
    }
}