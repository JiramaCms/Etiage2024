<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ManagerRegistry $mr): Response
    {
        $allProduct = $mr->getRepository(Product::class)->findAll();
        //$Product = $mr->getRepository(Product::class)->findOneBy(['name' => 'product 2']);
        return $this->render('product/index.html.twig', [
            'allProduct' => $allProduct,
            //'prod' => $Product,
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
