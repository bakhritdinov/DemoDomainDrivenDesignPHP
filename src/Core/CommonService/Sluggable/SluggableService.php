<?php

namespace App\Core\CommonService\Sluggable;

use App\Core\CommonService\Sluggable\Attributes\Sluggable;

class SluggableService
{
    public function hasSluggable(string $field, string $class): bool
    {
        $properties = (new \ReflectionClass($class))->getProperties();

        foreach ($properties as $property) {
            if ($property->getName() === $field) {
                return !empty($property->getAttributes(Sluggable::class));
            }
        }

        return false;
    }

    public function getSluggableFieldName(string $class): ?string
    {
        $properties = (new \ReflectionClass($class))->getProperties();

        foreach ($properties as $property) {
            if (!empty($property->getAttributes(Sluggable::class))) {
                return $property->getName();
            }
        }

        return null;
    }

    public function getSluggableFieldValue(string $class)
    {
        $properties = (new \ReflectionClass($class))->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(Sluggable::class);
            foreach ($attributes as $attribute) {
                foreach ($attribute->getArguments() as $argument) {
                    return $argument;
                }
            }
        }

        return null;
    }
}