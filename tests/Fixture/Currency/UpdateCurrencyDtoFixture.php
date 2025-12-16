<?php

namespace App\Tests\Fixture\Currency;

use App\Core\Domain\Currency\Dto\UpdateCurrencyDto;

class UpdateCurrencyDtoFixture
{
    public static function getOne(
        ?string $code = null,
        ?int    $num = null,
        ?string $name = null,
        ?bool   $isActive = null
    ): UpdateCurrencyDto
    {
        return new UpdateCurrencyDto(
            $code, $num, $name, $isActive
        );
    }
}