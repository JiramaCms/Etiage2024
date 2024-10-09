<?php
namespace App\Service;

use App\Entity\Site;
use App\Entity\Besoin;
use App\Entity\Station;
use App\Entity\Production;
use App\Entity\SiteProduction;
use App\Repository\BesoinRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SiteProductionRepository;
use Symfony\Component\HttpClient\HttpClient;

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
    public function gapPrevision($siteId, $production): float
    {
        // Vérifier que la clé 'predictions' existe dans le tableau de production
        if (!isset($production['predictions']) || count($production['predictions']) === 0) {
            throw new \InvalidArgumentException('La production ne doit pas être vide.');
        }

        $predictions = $production['predictions'];
        
        // Faire la somme de toutes les prévisions
        $totalPrevision = array_sum(array_column($predictions, 'prediction'));
        $nombrePrevisions = count($predictions);
        
        if ($nombrePrevisions === 0) {
            throw new \InvalidArgumentException('La production ne doit pas être vide.');
        }

        // Calculer la production moyenne d'eau
        $prodEau = $totalPrevision / $nombrePrevisions;

        // Prendre la dernière date de production
        $dernierElement = end($predictions);

        // Vérifier que la clé 'date' existe dans le dernier élément
        if (!isset($dernierElement['date'])) {
            throw new \InvalidArgumentException('La date est manquante dans la production.');
        }
        $derniereDateProduction = \DateTime::createFromFormat('d/m/Y', $dernierElement['date']);
        $besoin = $this->getBesoinForProduction($siteId, $derniereDateProduction);
        $gap = ($prodEau - $besoin) / $prodEau;
        return round($gap, 2);
    }
    public function calulateEtatSitePrevision(Site $site,$production)
    {
        $gap = $this->gapPrevision($site->getId(), $production);
        if($gap<-0.5){
            $site->setEtat(4);
        }
        elseif($gap < -0.25 && $gap > -0.5){
            $site->setEtat(3);
        }
        elseif(0 > $gap &&  $gap > -0.25){
            $site->setEtat(2);
        }
        elseif($gap > 0){
            $site->setEtat(1);
        }
        else{
            $site->setEtat(-1);
        }
        return $site;
    }

    public function calulateEtatSite(Site $site)
    {

        $productions = $this->siteProductionRepository->findProductionsBySite($site->getId());
        
        $firstProduction = $productions[0] ?? null;  // Récupérer la première production, ou null s'il n'y en a pas
        if ($firstProduction) {
            $gap = $this->calculateGap($firstProduction);
            $firstProduction->setGap($gap);
        }
        else{
            $site->setEtat(-1);
            return $site;
        }
        if($gap<-0.5){
            $site->setEtat(4);
        }
        elseif($gap < -0.25 && $gap > -0.5){
            $site->setEtat(3);
        }
        elseif(0 > $gap &&  $gap > -0.25){
            $site->setEtat(2);
        }
        elseif($gap > 0){
            $site->setEtat(1);
        }
        else{
            $site->setEtat(-1);
        }
        return $site;
    }
    public function makePrevision($stationId,$start,$end,EntityManagerInterface $entityManager){

        $rstation = $entityManager->getRepository(Station::class);
        $station = $rstation->findOneBy(['id' => $stationId]); // Utiliser un tableau associatif pour rechercher par ID
        $sources = $station->getSources(); // Récupérer la collection de sources
        $sourceNames = [];
        $sourcesArray = $sources->toArray(); // Convertir la collection en tableau
        foreach ($sourcesArray as $index => $source) {
            if ($index === 0) {
                $sourceNames[] = $source->getNom(); // Garder le premier nom tel quel
            } else {
                $sourceNames[] = strtolower($source->getNom()); // Mettre les autres noms en minuscules
            }
        }
        $sourceNamesString = implode('/', $sourceNames);
        $siteCode = $station->getSite()->getCode();
        $stationId = $station->getCode();
        $siteId = $siteCode;
        $source = $sourceNamesString;
    
        $data = [];
        $interval = new \DateInterval('P1D');
        $datePeriod = new \DatePeriod($start, $interval, $end);
    
        foreach ($datePeriod as $date) {
            $data[] = [
                'station_id' => $stationId,
                'site_id' => $siteId,
                'source' => $source,
                'year' => $date->format('Y'),
                'month' => $date->format('m'),
                'day' => $date->format('d'),
            ];
        }
        $client = HttpClient::create();
        $response = $client->request('POST', 'http://127.0.0.1:5000/predict', [
            'json' => $data,
        ]);
    
        $rep = $response->toArray();
        return $rep;
    }
}
