<?php

namespace App\Infrastructure\DBAL\Doctrine\Listener\Language\Normalizer;

use App\Core\Domain\Language\Entity\Language;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpdateLanguageNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return json_encode(
            [
                'id' => $object->getId()->toRfc4122(),
                'name' => $object->getName(),
                'logo' => $object->getLogo(),
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