<?php

namespace App\Infrastructure\Http\Country\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CountryListNormalizer implements NormalizerInterface
{
    public function __construct(public CountryNormalizer $countryNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return array_map(function ($country) use ($format, $context) {
            return $this->countryNormalizer->normalize($country, $format, $context);
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