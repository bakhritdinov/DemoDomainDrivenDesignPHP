<?php

namespace App\Infrastructure\DBAL\Doctrine\Listener\Language\Normalizer;

use App\Core\Domain\Language\Entity\Language;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateLanguageNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return json_encode(
            [
                'id' => $object->getId()->toRfc4122(),
                'name' => $object->getName(),
                'code' => $object->getCode(),
                'logo' => $object->getLogo(),
                'createdAt' => $object->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ]
        );
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