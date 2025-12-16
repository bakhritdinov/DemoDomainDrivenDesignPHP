<?php

namespace App\Infrastructure\Http\Language\v1\Response\Normalizer;

use App\Core\Domain\Language\Entity\Language;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LanguageNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()->toRfc4122(),
            'name' => $object->getName(),
            'code' => $object->getCode(),
            'logo' => $object->getLogo(),
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
            Language::class
        ];
    }
}