<?php
namespace App\Controller;
ini_set('memory_limit', '256M');

use App\Entity\Besoin;
use App\Util\Util;
use App\Entity\Site;
use App\Entity\Zone;
use App\Entity\Source;
use App\Entity\Station;
use App\Entity\Production;
use App\Form\SiteFormType;
use App\Entity\SiteProduction;
use App\Entity\ProductionMonth;
use App\Service\ProductionService;
use App\Entity\StationProductionMonth;
use App\Service\ActionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SiteController extends AbstractController
{
    private $productionService;
    private $actionService;

    public function __construct(ProductionService $productionService,ActionService $actionService)
    {
        $this->productionService = $productionService;
        $this->actionService = $actionService;
    }
    
    #[Route('/site', name: 'app_insert_site')]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
        ]);
    }
    #[Route('/etiage', name: 'app_map_etiage')]
    public function etiageMap(EntityManagerInterface $entityManager): Response
    {
        $rsite =  $entityManager->getRepository(Site::class);
        $rzone = $entityManager->getRepository(Zone::class);
        $sites = $rsite->findAll();
        $zones = $rzone->findAll();
        $zone=(Util::toJson($zones));
        $site = (Util::toJson($sites));
        return $this->render('site/etiage.html.twig', [
            'sites' => $site,
            'zone' => $zone,
        ]);
    }
    #[Route('/predict-action', name: 'app_predict_etiage_action')]
    public function predictEtiageAction(EntityManagerInterface $entityManager,Request $request)
    {
        $datas = $request->getContent(); // Lire les données JSON envoyées
        $data = json_decode($datas, true);        
        //$start = new \DateTime($data['start-date']);
        //$end = new \DateTime($data['end-date']);
        $gap = $data['gap'];
        $production = $data['production'];  // Site ou zone sélectionné(e)
        $capacite = $gap * $production;
        // Appelle le modèle pour prédire l'étiage
        $solution = $this->actionService->bestActionToTakeWithoutBudget($capacite);
        dump($solution);
        $solutions = (Util::toJson($solution));
        dump($solutions);
        // Retourne la date au frontend
        return new JsonResponse($solutions);
    }
    #[Route('/predict-etiage', name: 'app_predict_etiage')]
    public function predictEtiage(EntityManagerInterface $entityManager,Request $request)
    {
        $datas = $request->getContent(); // Lire les données JSON envoyées
        $data = json_decode($datas, true);        
        //$start = new \DateTime($data['start-date']);
        //$end = new \DateTime($data['end-date']);
        $year = $data['year'];
        $siteZone = $data['siteLibelle'];  // Site ou zone sélectionné(e)
        $site = $entityManager->getRepository(Site::class)->findOneBy(['libelle' => $siteZone]);


        // Appelle le modèle pour prédire l'étiage
        $predictedDate = $this->productionService->makeEtiage($siteZone,$year,$entityManager);
        dump($predictedDate);
        $reponse = $this->productionService->finddateEtiage($predictedDate);
        //dump($reponse);
        $besoin = $this->productionService->getBesoinForProduction($site->getId(), new \DateTime($reponse['lowest']['date']));
        $gap = ($besoin - $reponse['lowest']['production']) / $besoin;
        $gap =  round($gap, 2);

        // Retourne la date au frontend
        return new JsonResponse(['etiage' => $reponse ,
                                  'gap' => $gap 
                                ]);
    }
    #[Route('/prevision', name: 'app_map_prevision')]
    public function previsionMap(EntityManagerInterface $entityManager): Response
    {
        $rsite =  $entityManager->getRepository(Site::class);
        $sites = $rsite->findAll();
        $site = (Util::toJson($sites));
        return $this->render('site/previsionMap.html.twig', [
            'sites' => $site,
        ]);
    }
    #[Route('update-zone-production', name : 'update_zone_production')]
    public function updateZoneProduction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // Vérifiez les clés correctes
        if (!$data || !isset($data['siteId'], $data['previsions'])) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }
        $siteID = $data['siteId'];
        $prediction = $data['previsions'];
        try {
            $besoin = $this->productionService->simulation($siteID, $prediction);
            return new JsonResponse(['success' => true, 'rep' => $besoin]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
            
    }
    #[Route('site/prevision/etiage', name : 'site_etiage_prevision')]
    public function siteEtiagePrediction(EntityManagerInterface $entityManager,Request $request): Response
    {   
        $startDate = $request->getContent(); // Lire les données JSON envoyées
        $data = json_decode($startDate, true);        
        $start = new \DateTime($data['start-date']);
        $end = new \DateTime($data['end-date']);
        $end->modify('+1 day');  // Inclure le dernier jour
        //$start = new \DateTime("2024-12-01");
        //$end = new \DateTime("2024-12-31");

        $rsite =  $entityManager->getRepository(Site::class);
        $sites = $rsite->findAll();
        dump($sites);
        $rep = [];
        // Boucle sur chaque site pour générer les prévisions
        foreach ($sites as $site) {
            // Vérifier que le site a des stations associées
            $stations = $site->getStations(); // getStations() doit être une méthode qui retourne les stations d'un site
            if (count($stations) > 0) {
                $station = $stations[0]; // Prendre la première station
                // Appeler le service de production pour générer la prévision
                $prevision = $this->productionService->makePrevision($station, $start, $end, $entityManager);
                $this->productionService->calulateEtatSitePrevision($site,$prevision);
                // Ajouter les résultats dans le tableau de réponse
                dump($site,$prevision);

                $rep[] = [
                    'site' =>$site, // ou autre méthode pour obtenir le nom du site
                    'prevision'=>$prevision,
                ];
            }
        }
        $reponse = (Util::toJson($rep));
        dump($rep);

    
        // Convertir le tableau en JSON et retourner la réponse
        return new JsonResponse($reponse);
    }
    #[Route('/site/prediction/{id}', name: 'app_prediction_production_site')]
    public function predictionProduction($id,EntityManagerInterface $entityManager): Response
    {
       $rsite = $entityManager->getRepository(Site::class);
        $site = $rsite->find($id);
        $siteJ = (Util::toJson($site));
        //dump($zoneOfSite,$site);die();
        return $this->render('site/predictionProd.html.twig', [
            'site' => $site,
            'sitej' => $siteJ,
        ]);
    }
    #[Route('site/predict/production', name : 'site_production_prediction')]
    public function siteProductionPrediction(EntityManagerInterface $entityManager,Request $request): Response
    {   
        $startDate = $request->getContent(); // Lire les données JSON envoyées
        $data = json_decode($startDate, true);        
        $start = new \DateTime($data['start-date']);
        $end = new \DateTime($data['end-date']);
        $end->modify('+1 day');  // Inclure le dernier jour

        $stationI = $data['stationId'];
        $rstation = $entityManager->getRepository(Station::class);
        $station = $rstation->findOneBy(['id' => $stationI]); // Utiliser un tableau associatif pour rechercher par ID
        if ($station === null) {
            // Si la station n'existe pas, retourne un tableau vide
            return new JsonResponse([
                'error' => 'Station not found',
                'data' => []
            ], 404);
        }
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
        
        // Joindre les noms avec un '/' comme séparateur
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
        $repon = Util::toJson($rep);
        return new JsonResponse($repon);
    }

    #[Route('/site/test', name: 'app_site_test')]
    public function test(EntityManagerInterface $entityManager): Response
    {
        return $this->render('site/test.html.twig', [

        ]);
    }


    #[Route('station/production/month/{stationId}', name : 'station_production_month')]
    public function stationProductionMonth($stationId,EntityManagerInterface $entityManager): Response
    {
        $rproduction = $entityManager->getRepository(StationProductionMonth::class);
        $production = $rproduction->findBy(['idStation' => $stationId],['mois' => 'ASC']);
        //dump($production);die();
        $rep = Util::toJson($production);
        return new JsonResponse($rep);
    }
    #[Route('site/production/month/{siteId}', name : 'site_production_month')]
    public function siteProductionMonth($siteId,EntityManagerInterface $entityManager): Response
    {
        $rproduction = $entityManager->getRepository(ProductionMonth::class);
        $production = $rproduction->findBy(['idSite' => $siteId],['mois' => 'ASC']);
        //dump($production);die();
        $rep = Util::toJson($production);
        return new JsonResponse($rep);
    }
    //Done mandeha*/
    #[Route('/site/production/day/{siteId}', name :'site_production_day')]
    public function getProductionByDay($siteId, EntityManagerInterface $entityManager): Response
    {
        $rproduction = $entityManager->getRepository(SiteProduction::class);
        $productions = $rproduction->findProductionsBySite($siteId);
        //dd($productions);
        
        foreach ($productions as $production) {
            $gap = $this->productionService->calculateGap($production);
            $production->setGap($gap);
        }

        $rep = (Util::toJson($productions));
        return new JsonResponse($rep);
    }
    //A VOIR
    #[Route('/station/production/day/{stationId}', name :'station_production_day')]
    public function getStationProductionByDay($stationId, EntityManagerInterface $entityManager): Response
    {
        $rproduction = $entityManager->getRepository(Production::class);
        $productions = $rproduction->findLatestProductionsByStationId($stationId,10);
        //dd($productions);
        
        $rep = (Util::toJson($productions));
        return new JsonResponse($rep);
    }
    
    #[Route('/updateSite/{id}', name:'app_update_site')]
    public function updateSite(Site $site,Request $req,ManagerRegistry $mr): Response
    {
        $form = $this->createForm(SiteFormType::class,$site);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $site = $form->getData();
            //dump($site);die();
            $em = $mr->getManager();

            $em->persist($site);
            $em->flush();

            return $this->redirectToRoute('site_liste');
        }
        return $this->render('site/updateSite.html.twig',[
            'form'=>$form->createView()
        ]);
    }

     #[Route('/site/liste', name: 'site_liste')]
    public function site(Request $request,EntityManagerInterface $entityManager): Response
    {
        $rsite =  $entityManager->getRepository(Site::class);
        //$rzone = $entityManager->getRepository(Zone::class);
        $sites = $rsite->findAll();
        //$this->productionService->calulateEtatSite($sites[1]);
        foreach ($sites as $site) {
            $this->productionService->calulateEtatSite($site);
        }
        //dd($sites);
        //$zones = $rzone->findAll();
        //$zone=(Util::toJson($sites));
        $site = (Util::toJson($sites));
       // dump($site);die();
        return $this->render('site/site.html.twig', [
            'site' => $site,
        ]);
    }

    //prendre les zones d'une site 
    #[Route('site/liste/detailSite/{siteId}', name : 'site_liste_detail')]
    public function siteDetail($siteId,EntityManagerInterface $entityManager): Response
    {
        $rsite = $entityManager->getRepository(Site::class);
        //$siteByZone = $rzone->getSitesInZone($zoneId);
        $zoneOfSite = $rsite->getZoneOfSite($siteId);
        //dump($zoneOfSite);die();
        $ato = Util::toJson($zoneOfSite);

        return new JsonResponse($ato);
    }

    //prendre les sites d'une zone 
    #[Route('site/liste/detailZone/{zoneId}', name : 'site_liste_zone_detail')]
    public function zoneDetail($zoneId,EntityManagerInterface $entityManager): Response
    {
        $rzone = $entityManager->getRepository(Zone::class);
        //$siteByZone = $rzone->getSitesInZone($zoneId);
        $sitesInZone = $rzone->getSitesInZone($zoneId);
        //dump($zoneOfSite);die();
        $ato = Util::toJson($sitesInZone);

        return new JsonResponse($ato);
    }



    ///A effacer
    #[Route('/site/updateSite/{id}', name: 'update_site_poly')]
    public function updateSitePolygon(Site $site, EntityManagerInterface $entityManager): Response
    {
        $rsite = $entityManager->getRepository(Site::class);
        $queryBuilder = $rsite->createQueryBuilder('s')
            ->where('s.coord IS NOT NULL')
            ->getQuery();

        $sites = $queryBuilder->getResult();
        //dd($sites[1]->getCoord());

        $sitete=(Util::toJson($site));
        return $this->render('site/update-site-polygon.html.twig', [
            'sites' => $sitete,
            'site' => $site,// Coordonnées du polygone pour l'affichage
        ]);
    }
    #[Route('/site/updateSitePoly/{id}', name: 'update_site', methods: ['POST'])]
    public function saveUpdatedPolygon(Request $request, Site $site, EntityManagerInterface $entityManager): Response
    {
        $polygonCoords = $request->request->get('coords');
       // dd($polygonCoords);
        $site->setCoordByStr($polygonCoords);
        //dump($site);die();

        $entityManager->getRepository(Site::class)->update($site);

        return $this->redirectToRoute('update_site_poly', ['id' => $site->getId()]);
        
    }
    #[Route('/site/new', name: 'new_site_poly')]
    public function newSite(EntityManagerInterface $entityManager): Response
    {
        $rsite =  $entityManager->getRepository(Site::class);
        $sites = $rsite->findAll();
        $site = (Util::toJson($sites));
        return $this->render('site/new-zone.html.twig', [
            'sites' => $site,
            'message' => null,
        ]);
    }

    ///A effacer
    #[Route('/site/zone/insert', name: 'insert_zv', methods: ['POST'])]
    public function traitementbu(Request $request, EntityManagerInterface $em): Response
    {
        $libelle = $request->request->get('libelle');
        $description = $request->request->get('adresse');
        $coord = $request->request->get('coords');

        //echo "efa tonga" . $coord ;

        // Appeller l API ici avec comme zone $coords de format LatLng(-18.924224, 47.465464),LatLng(-18.921119, 47.465464),LatLng(-18.921119, 47.468769)

       /* $ctrl_message = null;
        if ($coords) {
            $zoneVente = new Zone($libelle, $apropos, $coords);
            echo($zoneVente->getCoordToWKT());

            $intersect = $em->getRepository(Zone::class)->doesPolygonIntersect($zoneVente);

             if($intersect) {
                 $ctrl_message = "Zone intersectee";
             } else {
                 $em->getRepository(Zone::class)->insert($zoneVente);
            }
        }*/
        $site = new Site($libelle, $description,$coord);
        //dump($zone->getCoordStrWKT());die();

        $em->getRepository(Site::class)->insert($site);

        // return $this->redirectToRoute('nouveau_zv');
        return $this->render('site/new-zone.html.twig', [
            'controller_name' => 'LocationController',
            'message' => "enregistrer",
        ]);
    }

    #[Route('/site/zone', name: 'All_zone')]
    public function listeZone(EntityManagerInterface $entityManager): Response
    {
        $rzone =  $entityManager->getRepository(Zone::class);
        $rsite = $entityManager->getRepository(Site::class);
        $site = $rsite->findAll();

        $zones = $rzone->findAll();
        $zone=(Util::toJson($zones));
        //dump($zone,$site);die();
        return $this->render('site/all-zone.html.twig', [
            'zone' => $zone,
            'zoneName' => $zones,
            'sites' => $site,
        ]);
    }
    #[Route('/site/{id}', name: 'detail_site')]
    public function detailSite($id,EntityManagerInterface $entityManager): Response
    {
        $rsite = $entityManager->getRepository(Site::class);
        $site = $rsite->find($id);
        $rzone = $entityManager->getRepository(Zone::class);
        $siteJ = (Util::toJson($site));
        $rbesoin = $entityManager->getRepository(Besoin::class);
        $besoin = $rbesoin->findBesoinForSiteLastDate($id);
        $besoinj = (Util::toJson($besoin));
        //dump($site->getIncidents()[0]->getLibelle());die();
        //dump($site,$siteJ);die();
        $zoneOfSite = $rzone->getZoneOfSite($id);
        //dump($zoneOfSite);die();
        //$zone = (Util::toJson($zoneOfSite));

        //dump($zoneOfSite,$site);die();
        return $this->render('site/detailSite.html.twig', [
            'site' => $site,
            'sitej' => $siteJ,
            'besoinj' => $besoinj,
            'zonej' => $zoneOfSite,
        ]);
    }    
    
}
