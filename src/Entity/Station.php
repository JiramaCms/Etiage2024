<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'Ce code est déjà utilisé.')] // Ajoute cette annotation
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(targetEntity: Production::class, mappedBy: 'station')]
    private Collection $production;

    #[ORM\ManyToOne(inversedBy: 'stations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToMany(targetEntity: Source::class, mappedBy: 'stations')]
    private Collection $sources;

    public function __construct()
    {
        $this->production = new ArrayCollection();
        $this->sources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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

    /**
     * @return Collection<int, Production>
     */
    public function getProduction(): Collection
    {
        return $this->production;
    }

    public function addProduction(Production $production): static
    {
        if (!$this->production->contains($production)) {
            $this->production->add($production);
            $production->setStation($this);
        }

        return $this;
    }

    public function removeProduction(Production $production): static
    {
        if ($this->production->removeElement($production)) {
            // set the owning side to null (unless already changed)
            if ($production->getStation() === $this) {
                $production->setStation(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Source>
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): static
    {
        if (!$this->sources->contains($source)) {
            $this->sources->add($source);
            $source->addStation($this);
        }

        return $this;
    }

    public function removeSource(Source $source): static
    {
        if ($this->sources->removeElement($source)) {
            $source->removeStation($this);
        }

        return $this;
    }
}
