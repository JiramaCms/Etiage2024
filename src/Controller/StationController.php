<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Station;
use App\Form\StationFormType;
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
            return $this->redirectToRoute('app_liste_objectif');
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
                    $newstation->addSource($newSource);  // Ajoute la source à la station
                    $newSource->addStation($newstation); // Ajoute la station à la source (bidirectionnel)
            }
    
            // Persister et enregistrer les modifications
            $em->persist($station);
            $em->persist($newstation);
            $em->flush();

        return $this->redirectToRoute('app_liste_objectif');
    }

    return $this->render('station/updateStation.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
