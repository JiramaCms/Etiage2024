<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Incident;
use App\Entity\Observation;
use App\Form\IncidentFormType;
use App\Form\ObservationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ObservationController extends AbstractController
{
    #[Route('/observation', name: 'app_liste_observation')]
    public function index(ManagerRegistry $mr): Response
    {
        $allObservation= $mr->getRepository(Observation::class)->findAll();
        //$Product = $mr->getRepository(Objectif::class)->findOneBy(['libelle' => 'objectif 2']);
        return $this->render('observation/index.html.twig', [
            'observations' => $allObservation,
        ]);
    }

    #[Route('/addObservation',name: 'app_add_observation')]
    public function addObservation(Request $req,ManagerRegistry $mr): Response
    {

        $observation = new Observation();
        $form = $this->createForm(ObservationFormType::class,$observation);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $observation = $form->getData();
            $em = $mr->getManager();

            $em->persist($observation);
            $em->flush();
            return $this->redirectToRoute('app_liste_observation');
        }

        return $this->render('observation/addObservation.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/addObservation/{id}',name: 'app_add_observationByAction')]
    public function addObservationID($id,Request $req,ManagerRegistry $mr): Response
    {
        $em = $mr->getManager();
        // Récupérer l'entité Action en fonction de l'id
        $action = $em->getRepository(Action::class)->find($id);
        if (!$action) {
            throw $this->createNotFoundException('No action found for id ' . $id);
        }
        // Créer une nouvelle observation et lier l'action
        $observation = new Observation();
        $observation->setAction($action); // Associer l'action à l'observation
        $form = $this->createForm(ObservationFormType::class,$observation,[
            'getIdByUrl' => true,
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $observation = $form->getData();
            $em = $mr->getManager();

            $em->persist($observation);
            $em->flush();
            return $this->redirectToRoute('app_liste_action');
        }

        return $this->render('observation/addObservation.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
