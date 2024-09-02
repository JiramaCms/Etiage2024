<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Objectif;
use App\Form\ObjectifFormType;
use App\Service\ActionService;
use App\Repository\ObjectifRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ObjectifController extends AbstractController
{
    private $actionService;

    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
    }
    
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
    public function objectifById($id,ManagerRegistry $mr): Response
    {
        $objectif = $mr->getRepository(Objectif::class)->find($id);
        //$t= $objectif->getActions();
        //dump($t->get(0));die();

        return $this->render('objectif/objectif.html.twig', [
            'objectif' => $objectif,
        ]);
    }

    #[Route('/site/{siteId}/objectif', name: 'app_site_objectifs')]
    public function objectifsySite(int $siteId, ObjectifRepository $objectifRepository): Response
    {
        // Utiliser le repository pour récupérer les objectifs
        $objectif = $objectifRepository->findBySiteId($siteId);
        dump($objectif);die();
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
   
    #[Route('/addObjectif/{id}',name: 'app_add_objectifBySite')]
    public function addObjectifID($id,Request $req,ManagerRegistry $mr): Response
    {
        $em = $mr->getManager();
        // Récupérer l'entité Action en fonction de l'id
        $site = $em->getRepository(Site::class)->find($id);
        if (!$site) {
            throw $this->createNotFoundException('Pas de site trouvé pour l\'id ' . $id);
        }
        // Créer une nouvelle observation et lier l'action
        $objectif = new Objectif();
        $objectif->setSite($site); // Associer le site à l'objectif
        $form = $this->createForm(ObjectifFormType::class,$objectif,[
            'getIdByUrl' => true,
        ]);
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

    #[Route('/updateObjectif/{id}', name:'app_update_objectif')]
    public function updateObjectif(Objectif $objectif,Request $req,ManagerRegistry $mr): Response
    {
        $form = $this->createForm(ObjectifFormType::class,$objectif);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $objectif = $form->getData();
            $em = $mr->getManager();

            $em->persist($objectif);
            $em->flush();

            return $this->redirectToRoute('app_liste_objectif');
        }
        return $this->render('objectif/updateMateriel.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/objectif/solution/{id}', name: 'app_liste_objectif')]
    public function getSolutionsMinimales(Objectif $objectif,ManagerRegistry $mr): Response
    {
        $types = $this->actionService->bestActionToTake($objectif);
        dump($types);die();
        return $this->render('objectif/index.html.twig', [
            
        ]);
    }

}
