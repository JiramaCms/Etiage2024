<?php
namespace App\Service;

use App\Entity\Besoin;
use App\Entity\Production;
use App\Entity\SiteProduction;
use App\Repository\BesoinRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductionService
{
    private $besoinRepository;

    public function __construct(BesoinRepository $besoinRepository)
    {
        $this->besoinRepository = $besoinRepository;
    }

    public function getBesoinForProduction($siteId, \DateTimeInterface $dateProduction): float
    {
        $besoin = $this->besoinRepository->findBesoinForSiteAndDate($siteId, $dateProduction);
        return $besoin ? $besoin->getQuantite() : 0.0;
    }

    public function calculateGap(SiteProduction $production): float
    {
        $besoin = $this->getBesoinForProduction($production->getSiteId(), $production->getDateProduction());
        $gap=($production->getSommeProduction() - $besoin)/$production->getSommeProduction();
        return round($gap, 2);;
    }
}
