<?php

namespace App\Util;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class Util
{
    public static function toJson($data)
    {
        $encoders = [new JsonEncoder()];

        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $json = $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return $json;
    }

    public static function toJsonData($data)
    {
        // Si $data est un tableau d'entités, on le convertit en tableau avant de le sérialiser
        if (is_array($data)) {
            return json_encode(array_map(function($entity) {
                // Assurez-vous d'utiliser une méthode pour obtenir les données de l'entité
                return $entity->toArray(); // Vous devez implémenter toArray() dans vos entités
            }, $data));
        }
        
        // Si c'est une seule entité
        return json_encode($data->toArray());
    }
}