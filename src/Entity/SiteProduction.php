<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SiteProductionRepository;


#[ORM\Entity(repositoryClass: SiteProductionRepository::class,readOnly: true)]
#[ORM\Table(name: "siteproduction")]
class SiteProduction
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private ?int $site_id = null;

    #[ORM\Id]
    #[ORM\Column]
    private ?string $date_production = null;

    #[ORM\Id]
    #[ORM\Column(type: "float")]
    private ?float $somme_production = null;

    private ?float $gap = null;

    // Getters and Setters

    public function getSiteId(): ?int
    {
        return $this->site_id;
    }

    public function setSiteId(int $site_id): self
    {
        $this->site_id = $site_id;

        return $this;
    }

    public function getDateProduction(): ?DateTimeInterface
    {
        if ($this->date_production === null) {
            return null;
        }
        // Convert the string to DateTime
        return DateTime::createFromFormat('Y-m-d', $this->date_production);
    }

    public function setDateProduction(string $date_production): self
    {
        $this->date_production = $date_production;

        return $this;
    }

    public function getSommeProduction(): ?float
    {
        return $this->somme_production;
    }

    public function setSommeProduction(float $somme_production): self
    {
        $this->somme_production = $somme_production;

        return $this;
    }

    public function getGap(): ?float
    {
        return $this->gap;
    }

    public function setgap(float $gap): self
    {
        $this->gap = $gap;

        return $this;
    }
}