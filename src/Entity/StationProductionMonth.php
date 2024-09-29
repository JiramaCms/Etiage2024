<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StationProductionMonthRepository;

#[ORM\Entity(repositoryClass: StationProductionMonthRepository::class)]
#[ORM\Table(name: 'stationProductionMonth')]
class StationProductionMonth
{

    #[ORM\Column]
    private ?int $idStation = null;

    #[ORM\Id]
    #[ORM\Column]
    private ?string $mois = null;

    #[ORM\Id]
    #[ORM\Column]
    private int $quantite;


    public function getIdStation(): ?int
    {
        return $this->idStation;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }
    
    public function getMois(): ?DateTimeInterface
    {
        if ($this->mois === null) {
            return null;
        }
        // Convert the string to DateTime
        return DateTime::createFromFormat('Y-m', $this->mois);

    }

}
