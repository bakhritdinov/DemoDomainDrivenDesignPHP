<?php

namespace App\Core\Domain\Language\Dto;

final readonly class CreateLanguageDto
{
    public function __construct(
        public string  $name,
        public string  $code,
        public ?string $logo = null,
        public ?bool   $isActive = null,
    )
    {
    }

    public static function fromArray(array $data): CreateLanguageDto
    {
        return new self(
            $data['name'],
            $data['code'],
            $data['logo'] ?? null,
            $data['isActive'] ?? null
        );
    }
}