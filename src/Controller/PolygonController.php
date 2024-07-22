<?php

namespace App\Controller;

use App\Entity\Polygon;
use App\Form\PolygonType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PolygonController extends AbstractController
{

    #[Route('/polygon', name: 'polygon')]
    public function index(ManagerRegistry $mr): Response
    {
        $polygons = $mr->getRepository(Polygon::class)->findAll();
        // Transform polygons to arrays
        $polygonsArray = array_map(fn($polygon) => $polygon->toArray(), $polygons);

        // Encode polygons as JSON
        $polygonsJson = json_encode($polygonsArray, JSON_THROW_ON_ERROR);

        return $this->render('polygon/index.html.twig', [
            'polygons' => $polygonsJson,
            'polygonsName' =>$polygons,
        ]);
    }


    #[Route('/polygon/new', name: 'polygon_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $polygon = new Polygon();
        $form = $this->createForm(PolygonType::class, $polygon);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($polygon);
            $entityManager->flush();

            $this->addFlash('success', 'Polygon saved successfully!');

            return $this->redirectToRoute('polygon_new');
        }

        return $this->render('polygon/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

} 
