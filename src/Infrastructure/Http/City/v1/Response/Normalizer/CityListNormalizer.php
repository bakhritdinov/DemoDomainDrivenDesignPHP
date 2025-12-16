<?php

namespace App\Infrastructure\Http\City\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CityListNormalizer implements NormalizerInterface
{
    public function __construct(public CityNormalizer $cityNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return array_map(function ($city) {
            return $this->cityNormalizer->normalize($city);
        }, $object);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}