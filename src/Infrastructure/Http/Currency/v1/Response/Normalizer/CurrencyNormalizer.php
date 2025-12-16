<?php

namespace App\Infrastructure\Http\Currency\v1\Response\Normalizer;

use App\Core\Domain\Currency\Entity\Currency;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CurrencyNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()->toRfc4122(),
            'code' => $object->getCode(),
            'num' => $object->getNum(),
            'name' => $object->getName(),
            'isActive' => $object->isActive(),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
            'rates' => array_map(function ($currencyRate) {
                return [
                    'code' => $currencyRate->getCurrencyTo()->getCode(),
                    'rate' => $currencyRate->getRate()
                ];
            }, $object->getCurrencyRates()->toArray())
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Currency::class
        ];
    }
}