<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeFormType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TypeController extends AbstractController
{
    #[Route('/materiel', name: 'app_liste_materiel')]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
    {
         // Création de la requête avec QueryBuilder
         $materiauxQuery = $entityManager->getRepository(Type::class)->createQueryBuilder('t')->getQuery();

         // Pagination
         $materiaux = $paginator->paginate(
             $materiauxQuery,
             $request->query->getInt('page', 1),
             10 // Nombre d'éléments par page
         );
 
         return $this->render('materiel/index.html.twig', [
             'materiaux' => $materiaux,
         ]);
    }

    #[Route('/addMateriel',name: 'app_add_materiel')]
    public function addMateriel(Request $req,ManagerRegistry $mr): Response
    {

        $materiel = new Type();
        $form = $this->createForm(TypeFormType::class,$materiel);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $materiel = $form->getData();
            $materiel->setDisposition(true);
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
    public function updateMateriel(Type $materiel,Request $req,ManagerRegistry $mr): Response
    {
        $form = $this->createForm(TypeFormType::class,$materiel,[
            'is_edit' => true,
        ]);
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
