<?php

namespace App\Controller;

use App\Entity\Incident;
use App\Form\IncidentFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IncidentController extends AbstractController
{
    #[Route('/incident', name: 'app_liste_incident')]
    public function index(ManagerRegistry $mr): Response
    {
        $allIncident= $mr->getRepository(Incident::class)->findAll();
        //$Product = $mr->getRepository(Objectif::class)->findOneBy(['libelle' => 'objectif 2']);
        return $this->render('incident/index.html.twig', [
            'incidents' => $allIncident,
        ]);
    }

    #[Route('/addIncident',name: 'app_add_incident')]
    public function addIncident(Request $req,ManagerRegistry $mr): Response
    {

        $incident = new Incident();
        $form = $this->createForm(IncidentFormType::class,$incident);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $incident = $form->getData();
            $em = $mr->getManager();

            $em->persist($incident);
            $em->flush();
            return $this->redirectToRoute('app_liste_objectif');
        }

        return $this->render('incident/addIncident.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
