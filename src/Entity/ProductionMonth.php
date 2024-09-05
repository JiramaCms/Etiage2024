<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductionMonthRepository;

#[ORM\Entity(repositoryClass: ProductionMonthRepository::class)]
#[ORM\Table(name: 'productionMonth')]
class ProductionMonth
{

    #[ORM\Column]
    private ?int $idSite = null;

    #[ORM\Id]
    #[ORM\Column]
    private ?string $mois = null;

    #[ORM\Id]
    #[ORM\Column]
    private int $quantite;


    public function getIdSite(): ?int
    {
        return $this->idSite;
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
