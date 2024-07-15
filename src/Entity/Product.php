<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message:"please write the name of the product"
    )]
    #[Assert\Length(
        min: 4,
        max: 50,
        minMessage: 'Your  name must be at least {{ limit }} characters long',
        maxMessage: 'Your  name cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(
        message:"please insert the number of product"
    )]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]#[Assert\NotBlank(
        message:"Mettez la description de votre produit"
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expiredAt = null;

    #[ORM\ManyToOne(inversedBy: 'products')]#[Assert\NotBlank(
        message:"S'il vous plait choisissez une categorie"
    )]
    private ?Categorie $classGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(?\DateTimeImmutable $expiredAt): static
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    public function getClassGroup(): ?Categorie
    {
        return $this->classGroup;
    }

    public function setClassGroup(?Categorie $classGroup): static
    {
        $this->classGroup = $classGroup;

        return $this;
    }
}
