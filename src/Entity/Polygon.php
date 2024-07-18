<?php

namespace App\Entity;


use App\Repository\PolygonRepository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PolygonRepository::class)]
class Polygon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $coordinates = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoordinates(): ?string
    {
        return $this->coordinates;
    }

    public function setCoordinates(string $coordinates): static
    {
        $this->coordinates = $coordinates;

        return $this;
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

    public function isPointInPolygon($point)
    {
        $coordinates = json_decode($this->coordinates, true);
        $x = $point[0];
        $y = $point[1];
        $inside = false;

        for ($i = 0, $j = count($coordinates) - 1; $i < count($coordinates); $j = $i++) {
            $xi = $coordinates[$i][0];
            $yi = $coordinates[$i][1];
            $xj = $coordinates[$j][0];
            $yj = $coordinates[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
