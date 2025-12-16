<?php

namespace App\Infrastructure\Http\Language\v1\Response\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LanguagePaginateNormalizer implements NormalizerInterface
{
    public function __construct(public LanguageNormalizer $languageNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): ?array
    {
        return [
            'data' => array_map(function ($language) use ($format, $context) {
                return $this->languageNormalizer->normalize($language, $format, $context);
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