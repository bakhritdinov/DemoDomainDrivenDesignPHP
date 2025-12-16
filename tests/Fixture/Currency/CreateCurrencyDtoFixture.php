<?php

namespace App\Tests\Fixture\Currency;


use App\Core\Domain\Currency\Dto\CreateCurrencyDto;

class CreateCurrencyDtoFixture
{
    public static function getOne(
        string $code,
        int    $num,
        string $name,
        ?bool  $isActive = null
    ): CreateCurrencyDto
    {
        return new CreateCurrencyDto(
            $code, $num, $name, $isActive
        );
    }
}