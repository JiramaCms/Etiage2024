<?php
namespace App\Service;

use App\Entity\Site;
use App\Entity\Besoin;
use App\Entity\Production;
use App\Entity\SiteProduction;
use App\Repository\BesoinRepository;
use App\Repository\SiteProductionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductionService
{
    private $besoinRepository;
    private $siteProductionRepository;

    public function __construct(BesoinRepository $besoinRepository,SiteProductionRepository $siteProductionRepository)
    {
        $this->besoinRepository = $besoinRepository;
        $this->siteProductionRepository = $siteProductionRepository;
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

    public function calulateEtatSite(Site $site)
    {

        $productions = $this->siteProductionRepository->findProductionsBySite($site->getId());
        //dd($productions);
        
        $firstProduction = $productions[0] ?? null;  // Récupérer la première production, ou null s'il n'y en a pas
        if ($firstProduction) {
            $gap = $this->calculateGap($firstProduction);
            $firstProduction->setGap($gap);
        }
        else{
            $site->setEtat(-1);
            return $site;
        }
        if($gap>0.5){
            $site->setEtat(0);
        }
        elseif($gap > 0 && $gap < 0.5){
            $site->setEtat(1);
        }
        elseif($gap < 0 && $gap > -0.5){
            $site->setEtat(2);
        }
        elseif($gap < -0.5 && $gap > -0.8){
            $site->setEtat(3);
        }
        elseif($gap < -0.8){
            $site->setEtat(4);
        }else{
            $site->setEtat(-1);
        }
        //dd($site->getEtat());

        return $site;
    }
}
