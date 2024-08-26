<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Objectif;
use App\Form\ActionFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActionController extends AbstractController
{
    #[Route('/action', name: 'app_liste_action')]
    public function index(ManagerRegistry $mr): Response
    {
        $allAction= $mr->getRepository(Action::class)->findAll();
        //dump($allAction);die();
        return $this->render('action/index.html.twig', [
            'actions' => $allAction,
        ]);
    }

    #[Route('/addAction',name: 'app_add_action')]
    public function addAction(Request $req,ManagerRegistry $mr): Response
    {

        $action = new Action();
        $form = $this->createForm(ActionFormType::class,$action,[
            'include_date_fin' => false,
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $action = $form->getData();
            $em = $mr->getManager();

            $em->persist($action);
            $em->flush();
            return $this->redirectToRoute('app_liste_action');
        }

        return $this->render('action/addAction.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/addAction/{id}',name: 'app_add_actionByObjectif')]
    public function addActionID($id,Request $req,ManagerRegistry $mr): Response
    {
        $em = $mr->getManager();
        // Récupérer l'entité Action en fonction de l'id
        $objectif = $em->getRepository(Objectif::class)->find($id);
        if (!$objectif) {
            throw $this->createNotFoundException('Pas d\'objectif trouvé pour l\'id ' . $id);
        }
        // Créer une nouvelle observation et lier l'action
        $action = new action();
        $action->setObjectif($objectif); // Associer le site à l'objectif
        $form = $this->createForm(ActionFormType::class,$action,[
            'getIdByUrl' => true,
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $action = $form->getData();
            $em = $mr->getManager();

            $em->persist($action);
            $em->flush();
            return $this->redirectToRoute('app_liste_objectif');
        }
        return $this->render('objectif/addAction.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('updateAction/{id}', name:'app_update_action')]
    public function updateAction(Action $action,Request $req,ManagerRegistry $mr): Response
    {
        $form = $this->createForm(ActionFormType::class,$action,[
            'include_date_fin' => true,
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $action = $form->getData();
            $em = $mr->getManager();

            $em->persist($action);
            $em->flush();

            return $this->redirectToRoute('app_liste_action');
        }
        return $this->render('action/updateAction.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
