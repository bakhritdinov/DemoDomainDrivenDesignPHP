<?php

namespace App\Application\Language\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindLanguageByIdDeactivatedQuery implements Query
{
    public function __construct(private Uuid $languageId)
    {
    }

    public function getLanguageId(): Uuid
    {
        return $this->languageId;
    }
}