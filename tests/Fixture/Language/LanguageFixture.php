<?php

namespace App\Tests\Fixture\Language;

use App\Core\Domain\Language\Entity\Language;
use Symfony\Component\Uid\Uuid;

class LanguageFixture
{
    public static function getOne(
        string $name,
        string $code,
        ?string $logo = null,
        ?bool $isActive = null,
        ?Uuid $id = null
    ): Language
    {
        $language = new Language($name, $code, $logo);

        if (!is_null($isActive)) {
            $language->changeIsActive($isActive);
        }

        $reflectionClass = new \ReflectionClass(Language::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($language, $id ?: Uuid::v1());

        return $language;
    }
}