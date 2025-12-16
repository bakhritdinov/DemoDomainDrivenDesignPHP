<?php

namespace App\Infrastructure\Http\Language\v1\Request\Denormalizer;

use App\Core\Domain\Language\Dto\UpdateLanguageDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UpdateLanguageDtoDenormalizer implements DenormalizerInterface
{

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): UpdateLanguageDto
    {
        return new UpdateLanguageDto(
            $data['name'] ?? null,
            $data['logo'] ?? null,
            $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === UpdateLanguageDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}