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
            $em = $mr->getManager();

            $em->persist($station);
            $em->flush();
            return $this->redirectToRoute('app_liste_objectif');
        }
        return $this->render('station/addStation.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/updateStation/{id}', name:'app_update_station')]
    public function updateObjectif(Station $station,Request $req,ManagerRegistry $mr): Response
    {
        $form = $this->createForm(StationFormType::class,$station);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $station = $form->getData();
            $em = $mr->getManager();

            $em->persist($station);
            $em->flush();

            return $this->redirectToRoute('app_liste_objectif');
        }
        return $this->render('station/updateStation.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
