<?php

namespace App\Controller;

use App\Entity\Point;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(ManagerRegistry $mr): Response
    {
        $allPoints = $mr->getRepository(Point::class)->findAll();
        return $this->render('map/index.html.twig', [
            'allPoints' => $allPoints,
        ]);
    }

    #[Route('/map/savePoint', name: 'point_save')]
    public function savePoint(Request $request,ManagerRegistry $mr): Response
    {
        $latitude = $request->request->get('latitude');
        $longitude = $request->request->get('longitude');
        $nom = $request->request->get('nom');

        if ($latitude && $longitude && $nom) {
            $points = new Point();
            $points->setLatitude((float) $latitude);
            $points->setLongitude((float) $longitude);
            $points->setNom($nom);
            $em = $mr->getManager();

            $em->persist($points);
            $em->flush();

            $this->addFlash('success', 'Location saved successfully!');
        }

        return $this->redirectToRoute('app_map');
    }

    #[Route('/editMap', name: 'app_map_edit')]
    public function editMap(ManagerRegistry $mr): Response
    {
        $allPoints = $mr->getRepository(Point::class)->findAll();
        return $this->render('map/editMap.html.twig', [
            'allPoints' => $allPoints,
        ]);
    }
}
