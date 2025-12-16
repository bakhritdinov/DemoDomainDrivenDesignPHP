<?php

namespace App\Application\Language\Query;

use App\Application\Query;

readonly class FindLanguageByCodeDeactivatedQuery implements Query
{
    public function __construct(private string $languageCode)
    {
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
}