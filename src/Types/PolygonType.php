<?php

namespace App\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PolygonType extends Type
{
    const POLYGON = 'polygon';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'POLYGON';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        // Utiliser unpack pour extraire les données du polygone forme binaire
        $data = unpack('x/x/x/x/corder/Ltype/d*', $value);

        $coordinates = [];

        // Recuperer les coordonnees
        // On compte a partir de 2 prcq les autre donnees sont des val. sup. util pour le decodage
        for ($i = 2; $i < count($data)-2; $i += 2) {
            $coordinates[] = [
                'latitude' => $data[$i],
                'longitude' => $data[$i + 1],
            ];
        }

        return $coordinates;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        // Convertir les coordonnées en valeur binaire
        // Ici, vous devez implémenter la logique pour convertir vos coordonnées en format binaire
        // Cette partie peut varier en fonction de la façon dont vous stockez vos données géographiques

        return pack('xxxxcLdd', 0, 1, $value['latitude'], $value['longitude']);
    }

    public function getName()
    {
        return self::POLYGON;
    }
}
