<?php

namespace App\Infrastructure\Http\Region\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegionPaginateNormalizer implements NormalizerInterface
{
    public function __construct(
        public RegionNormalizer $regionNormalizer
    )
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): ?array
    {
        return [
            'data' => array_map(function ($region) {
                return $this->regionNormalizer->normalize($region);
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