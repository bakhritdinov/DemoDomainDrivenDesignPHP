<?php

namespace App\Infrastructure\Http\Address\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddressPaginateNormalizer implements NormalizerInterface
{
    public function __construct(public AddressNormalizer $addressNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): ?array
    {
        return [
            'data' => array_map(function ($address) {
                return $this->addressNormalizer->normalize($address);
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