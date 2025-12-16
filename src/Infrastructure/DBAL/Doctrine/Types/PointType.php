<?php

namespace App\Infrastructure\DBAL\Doctrine\Types;

use App\Core\Domain\Address\ValueObject\Point;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PointType extends Type
{
    const POINT = 'point';

    public function getName(): string
    {
        return self::POINT;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Point
    {
        list($longitude, $latitude) = sscanf($value, '(%f,%f)');

        return new Point($latitude, $longitude);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Point) {
            $value = sprintf('(%F,%F)', $value->getLongitude(), $value->getLatitude());
        }

        return $value;
    }

    public function canRequireSQLConversion(): true
    {
        return true;
    }
}