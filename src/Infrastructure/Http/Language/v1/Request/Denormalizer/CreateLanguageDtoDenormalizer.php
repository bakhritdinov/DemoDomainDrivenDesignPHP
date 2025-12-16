<?php

namespace App\Infrastructure\Http\Language\v1\Request\Denormalizer;

use App\Core\Domain\Language\Dto\CreateLanguageDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateLanguageDtoDenormalizer implements DenormalizerInterface
{

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): CreateLanguageDto
    {
        return new CreateLanguageDto(
            $data['name'],
            $data['code'],
            $data['logo'] ?? null,
            $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CreateLanguageDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}