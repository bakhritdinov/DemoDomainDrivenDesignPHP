<?php

namespace App\Infrastructure\Http\Address\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddressListNormalizer implements NormalizerInterface
{
    public function __construct(public AddressNormalizer $addressNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return array_map(function ($address) {
            return $this->addressNormalizer->normalize($address);
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