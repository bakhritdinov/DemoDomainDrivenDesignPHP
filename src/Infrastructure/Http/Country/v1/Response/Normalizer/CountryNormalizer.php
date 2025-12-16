<?php

namespace App\Infrastructure\Http\Country\v1\Response\Normalizer;

use App\Core\Domain\Country\Entity\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CountryNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()->toRfc4122(),
            'name' => $object->getName(),
            'numericCode' => $object->getNumericCode(),
            'alpha2' => $object->getAlpha2(),
            'alpha3' => $object->getAlpha3(),
            'isActive' => $object->isActive(),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Country::class
        ];
    }
}