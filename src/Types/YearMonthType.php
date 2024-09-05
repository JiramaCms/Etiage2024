<?php 
namespace App\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class YearMonthType extends Type
{
    const YEAR_MONTH = 'year_month'; // Custom type name

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "CHAR(7)"; // Assumes a format like 'YYYY-MM'
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        // Convert the value to DateTime
        return \DateTime::createFromFormat('Y-m', $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        // Convert DateTime to 'Y-m' format
        return $value->format('Y-m');
    }

    public function getName()
    {
        return self::YEAR_MONTH;
    }
}
