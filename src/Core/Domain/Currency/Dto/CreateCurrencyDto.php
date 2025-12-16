<?php

namespace App\Core\Domain\Currency\Dto;

final readonly class CreateCurrencyDto
{
    public function __construct(
        public string $code,
        public int    $num,
        public string $name,
        public ?bool  $isActive = null
    )
    {
    }
}