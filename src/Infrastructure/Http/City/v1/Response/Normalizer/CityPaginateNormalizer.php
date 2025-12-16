<?php

namespace App\Infrastructure\Http\City\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CityPaginateNormalizer implements NormalizerInterface
{
    public function __construct(public CityNormalizer $cityNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): ?array
    {
        return [
            'data' => array_map(function ($city) {
                return $this->cityNormalizer->normalize($city);
            }, iterator_to_array($object['data'])),
            'total' => $object['total'],
            'pages' => $object['pages'],
        ];
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