<?php

namespace App\Controller;

use App\Entity\Incident;
use App\Entity\Site;
use App\Entity\Product;
use App\Entity\Objectif;
use App\Form\IncidentFormType;
use App\Form\ProductType;
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

    #[Route('/addIncident',name: 'app_add_incident')]
    public function addIncident(Request $req,ManagerRegistry $mr): Reponse
    {
        $allSite= $mr->getRepository(Site::class)->findAll();

        $incident = new Incident();
        $form = $this->createForm(IncidentFormType::class,$incident);

        return $this->render('product/addProduct.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/addProduct', name: 'app_add_product')]
    public function addProduct(Request $req,ManagerRegistry $mr): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $em = $mr->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/addProduct.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/updateProduct/{id}', name: 'app_update_product')]
    public function updateProduct(Product $product,Request $req,ManagerRegistry $mr): Response
    {
        //dump($id);die;
        //$product = $mr->getRepository(Product::class)->findOneBy(['id'=> $id]);
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $em = $mr->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/updateProduct.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/deleteProduct/{id}', name: 'app_delete_product')]
    public function deleteProduct(Product $product,ManagerRegistry $mr): Response
    {
        
            $em = $mr->getManager();

            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute('app_product');
    }
}
