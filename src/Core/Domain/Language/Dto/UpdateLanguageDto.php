<?php

namespace App\Core\Domain\Language\Dto;

final readonly class UpdateLanguageDto
{
    public function __construct(
        public ?string $name,
        public ?string $logo = null,
        public ?bool   $isActive = null,
    )
    {
    }

    public static function fromArray(array $data): UpdateLanguageDto
    {
        return new self(
            $data['name'] ?? null,
            $data['logo'] ?? null,
            $data['isActive'] ?? null
        );
    }
}