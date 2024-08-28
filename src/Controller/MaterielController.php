<?php

namespace App\Controller;

use App\Entity\Materiel;
use App\Form\MaterielFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MaterielController extends AbstractController
{
    #[Route('/materiel', name: 'app_liste_materiel')]
    public function index(ManagerRegistry $mr): Response
    {
        $allMateriel= $mr->getRepository(Materiel::class)->findAll();
        //dump($allAction);die();
        return $this->render('materiel/index.html.twig', [
            'materiaux' => $allMateriel,
        ]);
    }

    #[Route('/addMateriel',name: 'app_add_materiel')]
    public function addMateriel(Request $req,ManagerRegistry $mr): Response
    {

        $materiel = new Materiel();
        $form = $this->createForm(MaterielFormType::class,$materiel);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $materiel = $form->getData();
            $em = $mr->getManager();

            $em->persist($materiel);
            $em->flush();
            return $this->redirectToRoute('app_liste_materiel');
        }

        return $this->render('materiel/addMateriel.html.twig', [
            'form'=>$form->createView()
        ]);
    }

   
    #[Route('/updateMateriel/{id}', name:'app_update_materiel')]
    public function updateMateriel(Materiel $materiel,Request $req,ManagerRegistry $mr): Response
    {
        $form = $this->createForm(MaterielFormType::class,$materiel);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $materiel = $form->getData();
            $em = $mr->getManager();

            $em->persist($materiel);
            $em->flush();

            return $this->redirectToRoute('app_liste_materiel');
        }
        return $this->render('materiel/updateMateriel.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    
}
