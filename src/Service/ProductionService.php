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
use Doctrine\ORM\EntityManager;
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
        $gap=($besoin - $production->getSommeProduction())/$besoin;
        return round($gap, 2);;
    }
    public function simulation($siteId,$prevision){
       
        try {
            $lastElement = end($prevision);
            // Vérifier si le tableau n'est pas vide et que l'élément a une propriété 'date'
            if ($lastElement && isset($lastElement['date'])) {
                $dateProduction = \DateTime::createFromFormat('d/m/Y', $lastElement['date']);
                //dump($dateProduction);
                $besoin = $this->getBesoinForProduction($siteId,$dateProduction);
                $totalPrevision = array_sum(array_column($prevision, 'prediction'));
                $nombrePrevisions = count($prevision);
                $prodEau = $totalPrevision / $nombrePrevisions;
                return [
                    'besoin' => $besoin,
                    'production' => $prodEau,
                ];
            }
        } catch (\Exception $e) {
            throw new \RuntimeException("Error in simulation: " . $e->getMessage());
        }
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
        $gap = ($besoin - $prodEau) / $besoin;
        return round($gap, 2);
    }
    public function calulateEtat(Site $site,$production)
    {
        $gap = $this->gapPrevision($site->getId(), $production);
        if($gap>0.5){
            $site->setEtat(4);
        }
        elseif($gap > 0.25 && $gap < 0.5){
            $site->setEtat(3);
        }
        elseif(0 < $gap &&  $gap < 0.25){
            $site->setEtat(2);
        }
        elseif($gap < 0){
            $site->setEtat(1);
        }
        else{
            $site->setEtat(-1);
        }
        return $site;
    }
    public function calulateEtatSitePrevision(Site $site,$production)
    {
        $gap = $this->gapPrevision($site->getId(), $production);
        if($gap>0.5){
            $site->setEtat(4);
        }
        elseif($gap > 0.25 && $gap < 0.5){
            $site->setEtat(3);
        }
        elseif(0 < $gap &&  $gap < 0.25){
            $site->setEtat(2);
        }
        elseif($gap < 0){
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
        if($gap>0.5){
            $site->setEtat(4);
        }
        elseif($gap > 0.25 && $gap < 0.5){
            $site->setEtat(3);
        }
        elseif(0 < $gap &&  $gap < 0.25){
            $site->setEtat(2);
        }
        elseif($gap < 0){
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
    public function makeEtiage($siteLibelle, $annee, EntityManagerInterface $entityManager) {
        // Récupérer le repository du Site et chercher le site par son libellé

        $site = $entityManager->getRepository(Site::class)->findOneBy(['libelle' => $siteLibelle]);
        if (!$site) {
            throw new \Exception("Site not found with libelle: " . $siteLibelle);
        }
    
        // Récupérer les stations associées au site
        $stations = $site->getStations();
        
        if (count($stations) > 0) {
            $station = $stations[0]; // Prendre la première station
            $rep = $this->makeEtiageStation($station->getId(), $annee, $entityManager);
            return $rep; // Convertir le résultat en tableau
        }
    
        return null; // Retourner null si aucune station n'est trouvée
    }
    public function finddateEtiage($data)
    {
        $lowestPrediction = null;
        $latestPrediction = null;
        $firstDate = null;    // Stocke la première date à partir d'août
        $lastDate = null;
        $hasDateInTargetMonths = false;   
        foreach ($data as $predictions) {
            foreach ($predictions as $prediction) {
            dump($prediction);
            if (!isset($prediction['date'])) {
                // Si la clé 'date' n'existe pas, continuez à la prochaine itération
                continue;
            }

            // Convertir la date en objet DateTime et récupérer le mois
            $date = new \DateTime($prediction['date']);
            $month = (new \DateTime($prediction['date']))->format('m');

            // Vérification si la date est dans les mois juillet, d'août, septembre ou octobre
            if (in_array($month, ['07','08', '09', '10'])) {
                $hasDateInTargetMonths = true;  
                // Trouver la production la plus faible
                if ($lowestPrediction === null || $prediction['production'] < $lowestPrediction['production']) {
                    $lowestPrediction = $prediction;
                }

                // Stocker la prédiction la plus récente
                if ($latestPrediction === null || $prediction['date'] < $latestPrediction['date']) {
                    $latestPrediction = $prediction;
                }

                // Trouver la première date (qui doit être en août ou après)
                if ($firstDate === null || $date < $firstDate) {
                    $firstDate = $date;
                }
            }

            if ($lastDate === null || $date > $lastDate) {
                $lastDate = $date;
            }
        }
    }
    if (!$hasDateInTargetMonths) {
        // Si aucune date dans les mois cibles, trouver la première et la dernière date globales
        foreach ($data as $predictions) {
            foreach ($predictions as $prediction) {
                if (!isset($prediction['date'])) {
                    continue;
                }
                // Convertir la date en objet DateTime
                $date = new \DateTime($prediction['date']);

                // Trouver la production la plus faible globale
                if ($lowestPrediction === null || $prediction['production'] < $lowestPrediction['production']) {
                    $lowestPrediction = $prediction;
                }

                // Stocker la prédiction la plus récente globale
                if ($latestPrediction === null || $prediction['date'] < $latestPrediction['date']) {
                    $latestPrediction = $prediction;
                }

                // Trouver la première date globale
                if ($firstDate === null || $date < $firstDate) {
                    $firstDate = $date;
                }

                // Trouver la dernière date globale
                if ($lastDate === null || $date > $lastDate) {
                    $lastDate = $date;
                }
            }
        }
    }
    // Calculer le nombre de jours entre la première et la dernière date
    if ($firstDate !== null && $lastDate !== null) {
        $interval = $firstDate->diff($lastDate);
        $daysBetween = $interval->days;  // Nombre total de jours
    } else {
        $daysBetween = 0;  // Si aucune date n'a été trouvée, retourner 0
    }

    return ['lowest' => $lowestPrediction, 'latest' => $latestPrediction, 'daysBetween' => $daysBetween];
}

    public function makeEtiageStation($stationId,$annee,EntityManagerInterface $entityManager){

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

        // Générer toutes les dates de l'année choisie
        $start = new \DateTime("{$annee}-01-01");
        $end = new \DateTime("{$annee}-12-31");
        $end->setTime(23, 59, 59);
    
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
        $response = $client->request('POST', 'http://127.0.0.1:5000/prevision/etiage', [
            'json' => $data,
        ]);
    
        $rep = $response->toArray();
        return $rep;
    }
}
