<?php

namespace App\Core\CommonService\Translatable;

use App\Core\CommonService\Translatable\Attributes\Translatable;

class TranslatableService
{
    public function hasTranslatable(string $field, string $class): bool
    {
        $properties = (new \ReflectionClass($class))->getProperties();

        foreach ($properties as $property) {
            if ($property->getName() === $field) {
                return !empty($property->getAttributes(Translatable::class));
            }
        }

        return false;
    }
}