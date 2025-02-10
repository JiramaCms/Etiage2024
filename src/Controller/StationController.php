<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Source;
use App\Entity\Station;
use App\Form\StationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StationController extends AbstractController
{
    #[Route('/station', name: 'app_station')]
    public function index(): Response
    {
        return $this->render('station/index.html.twig', [
            'controller_name' => 'StationController',
        ]);
    }
    #[Route('/station/form', name: 'app_insert_station')]
    public function newStationMap(EntityManagerInterface $entityManager): Response
    {
        $rsite =  $entityManager->getRepository(Site::class);
        $sites = $rsite->findAll();
        $rsource =  $entityManager->getRepository(Source::class);
        $sources = $rsource->findAll();
        return $this->render('station/index.html.twig', [
            'sites' => $sites,
            'sources' => $sources,
        ]);
    }
    #[Route('/station/insert', name: 'station_insert', methods: ['POST'])]
    public function insertStationMap(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupéreration des données du formulaire
        $libelle = $request->request->get('libelle');
        $code = $request->request->get('code');
        $coord = $request->request->get('coord');
        $siteId = $request->request->get('site');
        $sourcesIds = $request->request->all('sources');
        //dd($sourcesIds);
        
        // Convertir les coordonnées en tableau [latitude, longitude]
        $coordArray = explode(',', str_replace(['LatLng(', ')'], '', $coord));
        $latitude = floatval($coordArray[0]);
        $longitude = floatval($coordArray[1]);
        $rsite = $entityManager->getRepository(Site::class);
        $site = $rsite->find($siteId);

        // Créer une nouvelle instance de Site et définir ses propriétés
        $station = new Station();
        $station->setLibelle($libelle);
        $station->setCode($code);
        $station->setSite($site);
        $station->setCoord(['latitude' => $latitude, 'longitude' => $longitude]);
         // Insertion de la station et récupération de l'ID
        $stationId = $entityManager->getRepository(Station::class)->insert($station);

        // Insérer les relations dans la table de jointure
        foreach ($sourcesIds as $sourceId) {
            $entityManager->getRepository(Station::class)->insertStationSourceRelation($stationId, $sourceId);
        }


        // Rediriger vers
        return $this->redirectToRoute('app_insert_station');
    }

    #[Route('/station/updateStation/{id}', name: 'update_station_point')]
    public function updateStationMap(Station $station, EntityManagerInterface $entityManager): Response
    {
        $coords = $station->getCoord();
        $latitude = $coords ? $coords['latitude'] : null;
        $longitude = $coords ? $coords['longitude'] : null;
        $rsource =  $entityManager->getRepository(Source::class);
        $sources = $rsource->findAll();
        return $this->render('station/update-station-point.html.twig', [
            'station' => $station,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'sources' => $sources,
        ]);
    }
    #[Route('/station/update/{id}', name: 'station_update', methods: ['POST'])]
    public function updateStationPoint(Station $station,Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupéreration des données du formulaire
        $libelle = $request->request->get('libelle');
        $code = $request->request->get('code');
        $coord = $request->request->get('coord');
        $siteId = $request->request->get('site');
        $sourcesIds = $request->request->all('sources');

        
        // Convertir les coordonnées en tableau [latitude, longitude]
        $coordArray = explode(',', str_replace(['LatLng(', ')'], '', $coord));
        $latitude = floatval($coordArray[0]);
        $longitude = floatval($coordArray[1]);

        $site = $entityManager->getRepository(Site::class)->find($siteId);
        if (!$site) {
            throw $this->createNotFoundException('Le site n\'existe pas.');
        }

        // Créer une nouvelle instance de Site et définir ses propriétés
        $station->setLibelle($libelle);
        $station->setCode($code);
        $station->setSite($site);
        $station->setCoord(['latitude' => $latitude, 'longitude' => $longitude]);

        $entityManager->getRepository(Station::class)->update($station);

        $entityManager->getRepository(Station::class)->updateStationSourceRelations($station->getId(), $sourcesIds);

        // Rediriger vers
        return $this->redirectToRoute('update_station_point',['id' => $station->getId()]);
    }

    #[Route('/addStation/{id}',name: 'app_add_stationBySite')]
    public function addStationId($id,Request $req,ManagerRegistry $mr): Response
    {
        $em = $mr->getManager();
        // Récupérer l'entité Action en fonction de l'id
        $site = $em->getRepository(Site::class)->find($id);
        if (!$site) {
            throw $this->createNotFoundException('Pas de site trouvé pour l\'id ' . $id);
        }
        // Créer une nouvelle observation et lier l'action
        $station = new Station();
        $station->setSite($site); // Associer le site à l'objectif
        $form = $this->createForm(StationFormType::class,$station,[
            'getIdByUrl' => true,
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $station = $form->getData();
            ///pour le MANYTOMANY voici quoi faire
            foreach ($station->getSources() as $source) {
                $station->addSource($source); // Ajout explicite des sources
                $source->addStation($station);
            }
            //dd($station);

            $em->persist($station);
            $em->flush();
            return $this->redirectToRoute('site_liste');
        }
        return $this->render('station/addStation.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/updateStation/{id}', name: 'app_update_station')]
    public function updateStation($id, Request $request, ManagerRegistry $mr): Response
    {
        $em = $mr->getManager();
    
        // Récupérer l'entité Station à mettre à jour
        $station = $em->getRepository(Station::class)->find($id);
        if (!$station) {
            throw $this->createNotFoundException('Station non trouvée pour l\'id ' . $id);
        }
    
        // Créer le formulaire
        $form = $this->createForm(StationFormType::class, $station);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $newstation = $form->getData();

            // Supprimer les sources qui ne sont plus présentes
            foreach ($station->getSources() as $existingSource) {
                    $station->removeSource($existingSource);  // Supprime la source de la station
                    $existingSource->removeStation($station); // Supprime la station de la source (bidirectionnel)
            }
    
            // Ajouter les nouvelles sources
            foreach ($newstation->getSources() as $newSource) {
                    $station->addSource($newSource);  // Ajoute la source à la station
                    $newSource->addStation($station); // Ajoute la station à la source (bidirectionnel)
            }
    dd($station,$newstation);
            // Persister et enregistrer les modifications
            $em->persist($station);
            $em->persist($newstation);
            $em->flush();

        return $this->redirectToRoute('site_liste');
    }

    return $this->render('station/updateStation.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
