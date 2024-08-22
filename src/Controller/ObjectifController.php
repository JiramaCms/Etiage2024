<?php

namespace App\Controller;

use App\Entity\Objectif;
use App\Form\ObjectifFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ObjectifController extends AbstractController
{
    #[Route('/objectif', name: 'app_liste_objectif')]
    public function index(ManagerRegistry $mr): Response
    {
        $allObjectif= $mr->getRepository(Objectif::class)->findAll();
        //$Product = $mr->getRepository(Objectif::class)->findOneBy(['libelle' => 'objectif 2']);
        return $this->render('objectif/index.html.twig', [
            'objectifs' => $allObjectif,
        ]);
    }
    #[Route('/objectif/{id}' ,name: 'app_detail_observation')]
    public function observationById($id,ManagerRegistry $mr): Response
    {
        $objectif = $mr->getRepository(Objectif::class)->find($id);

        return $this->render('objectif/objectif.html.twig', [
            'objectif' => $objectif,
        ]);
    }

    #[Route('/addObjectif',name: 'app_add_objectif')]
    public function addIncident(Request $req,ManagerRegistry $mr): Response
    {

        $objectif = new Objectif();
        $form = $this->createForm(ObjectifFormType::class,$objectif);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $objectif = $form->getData();
            $em = $mr->getManager();

            $em->persist($objectif);
            $em->flush();
            return $this->redirectToRoute('app_liste_objectif');
        }

        return $this->render('objectif/addObjectif.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
