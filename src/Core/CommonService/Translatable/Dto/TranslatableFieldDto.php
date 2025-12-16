<?php

namespace App\Core\CommonService\Translatable\Dto;

final readonly class TranslatableFieldDto
{
    public function __construct(
        public string $field,
        public string $locale,
        public ?string $content = null
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['field'],
            $data['locale'],
            $data['content'] ?? null
        );
    }
}