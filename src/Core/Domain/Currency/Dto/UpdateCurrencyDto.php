<?php

namespace App\Core\Domain\Currency\Dto;

final readonly class UpdateCurrencyDto
{
    public function __construct(
        public ?string $code = null,
        public ?int    $num = null,
        public ?string $name = null,
        public ?bool   $isActive = null
    )
    {
    }
}
