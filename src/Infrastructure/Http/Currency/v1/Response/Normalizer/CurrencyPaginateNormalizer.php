<?php

namespace App\Infrastructure\Http\Currency\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CurrencyPaginateNormalizer implements NormalizerInterface
{
    public function __construct(public CurrencyNormalizer $currencyNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'data' => array_map(function ($currency) use ($format, $context) {
                return $this->currencyNormalizer->normalize($currency, $format, $context);
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