<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'polygon')]
    private $coord = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat = null;

    #[ORM\OneToMany(targetEntity: Incident::class, mappedBy: 'Site')]
    private Collection $incidents;

    #[ORM\OneToMany(targetEntity: Objectif::class, mappedBy: 'site')]
    private Collection $objectifs;

    #[ORM\OneToMany(targetEntity: Station::class, mappedBy: 'site')]
    private Collection $stations;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $code = null;

    private $coord_str_wkt;

    public function __construct($libelle = "", $adresse = "", $str_coords = null)
    {
        $this->libelle = $libelle;
        $this->adresse = $adresse;
        if($str_coords) {
            $this->setCoordByStr($str_coords);
        }
        $this->incidents = new ArrayCollection();
        $this->objectifs = new ArrayCollection();
        $this->stations = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }
    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(?int $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
    public function setCoord($coord): static
    {
        $this->coord = $coord;

        return $this;
    }
    public function getCoord()
    {
        return $this->coord;

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
        if (is_null($this->coord) || !is_array($this->coord) || empty($this->coord)) {
            return ''; // Gérer le cas où il n'y a pas de coordonnées
        }
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

    /**
     * @return Collection<int, Incident>
     */
    public function getIncidents(): Collection
    {
        return $this->incidents;
    }

    public function addIncident(Incident $incident): static
    {
        if (!$this->incidents->contains($incident)) {
            $this->incidents->add($incident);
            $incident->setSite($this);
        }

        return $this;
    }

    public function removeIncident(Incident $incident): static
    {
        if ($this->incidents->removeElement($incident)) {
            // set the owning side to null (unless already changed)
            if ($incident->getSite() === $this) {
                $incident->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Objectif>
     */
    public function getObjectifs(): Collection
    {
        return $this->objectifs;
    }

    public function addObjectif(Objectif $objectif): static
    {
        if (!$this->objectifs->contains($objectif)) {
            $this->objectifs->add($objectif);
            $objectif->setSite($this);
        }

        return $this;
    }

    public function removeObjectif(Objectif $objectif): static
    {
        if ($this->objectifs->removeElement($objectif)) {
            // set the owning side to null (unless already changed)
            if ($objectif->getSite() === $this) {
                $objectif->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStations(): Collection
    {
        return $this->stations;
    }

    public function addStation(Station $station): static
    {
        if (!$this->stations->contains($station)) {
            $this->stations->add($station);
            $station->setSite($this);
        }

        return $this;
    }

    public function removeStation(Station $station): static
    {
        if ($this->stations->removeElement($station)) {
            // set the owning side to null (unless already changed)
            if ($station->getSite() === $this) {
                $station->setSite(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }
    
}
