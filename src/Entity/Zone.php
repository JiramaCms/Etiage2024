<?php

namespace App\Entity;

use App\Repository\ZoneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZoneRepository::class)]
class Zone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'polygon')]
    private $coord = null;

    private $coord_str_wkt;

    public function __construct($libelle = "", $description = "", $str_coords = null)
    {
        $this->libelle = $libelle;
        $this->description = $description;
        if($str_coords) {
            $this->setCoordByStr($str_coords);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCoord()
    {
        return $this->coord;
    }

    public function setCoord($coord): static
    {
        $this->coord = $coord;

        return $this;
    }

    public function getCoordStrWKT(): string
    {
        return $this->coord_str_wkt;
    }

    public function setCoordStrWKT($coord_str_wkt): void
    {
        $this->coord_str_wkt = $coord_str_wkt;
    }
    


    //WKT well known text
    public function setCoordByStr($coord_str){
         // Étape 1: Diviser la chaîne en points individuels
         $points = explode('),', $coord_str);

         $polygon = [];
         $polygon_str = 'POLYGON((';
 
         // Étape 2: Parcourir chaque point et extraire lat et lng
         foreach ($points as $point) {
             // Supprimer le préfixe "LatLng(" du premier point
             $point = str_replace('LatLng(', '', $point);
             
             // Extraire les valeurs de lat et lng
             list($lat, $lng) = explode(',', $point);
             
             // Supprimer la parenthèse finale du dernier point
             $lng = rtrim($lng, ')');
             
             // Ajouter au tableau polygon
             $polygon[] = ['latitude' => floatval($lat), 'longitude' => floatval($lng)];
             $polygon_str .= $lng . ' ' . $lat . ', ';
         }
         $this->coord = $polygon; 
 
         $polygon_str .= $this->coord[0]['latitude'] . ' ' . $this->coord[0]['longitude'] . '))';
         $this->coord_str_wkt = $polygon_str; 
    }

    public function getCoordToWKT(): string {
        $polygonWKT = 'POLYGON((';

        foreach ($this->coord as $coord) {
            $polygonWKT .= $coord['latitude'] . ' ' . $coord['longitude'] . ', ';
        }
        $polygonWKT .= $this->coord[0]['latitude'] . ' ' . $this->coord[0]['longitude'] . '))';
        // var_dump($polygonWKT);

        return $polygonWKT;
    }

    public function setCoordByJson($coordsJson): void
    {
        $coordsArray = json_decode($coordsJson, true);

        $polygon = [];
        $polygon_str = 'POLYGON((';
        foreach ($coordsArray[0] as $point) {
            $polygon[] = ['latitude' => $point['lat'], 'longitude' => $point['lng']];
            $polygon_str .= $point['lat'] . ' ' . $point['lng'] . ', ';
        }
        // Fermer le polygone en ajoutant le premier point à la fin
        $polygon_str .= $polygon[0]['latitude'] . ' ' . $polygon[0]['longitude'] . '))'; 

        $this->coord = $polygon;
        $this->coord_str_wkt = $polygon_str;
    }
}


