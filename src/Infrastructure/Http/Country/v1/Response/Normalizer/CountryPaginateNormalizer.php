<?php

namespace App\Infrastructure\Http\Country\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CountryPaginateNormalizer implements NormalizerInterface
{
    public function __construct(public CountryNormalizer $countryNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): ?array
    {
        return [
            'data' => array_map(function ($country) use ($format, $context) {
                return $this->countryNormalizer->normalize($country, $format, $context);
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