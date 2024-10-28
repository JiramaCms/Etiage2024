<?php
///A effacer
namespace App\Controller;

use App\Entity\Pointer;
use App\Entity\Polygon;
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
        $allPoints = $mr->getRepository(Pointer::class)->findAll();
        $polygons = $mr->getRepository(Polygon::class)->findAll();


        $pointsData = [];
        foreach ($allPoints as $point) {
            $isInside = false;
            foreach ($polygons as $polygon) {
                if ($polygon->isPointInPolygon([$point->getLatitude(), $point->getLongitude()])) {
                    $isInside = true;
                    break;
                }
            }
            $pointsData[] = [
                'latitude' => $point->getLatitude(),
                'longitude' => $point->getLongitude(),
                'name' => $point->getNom(),
                'isInside' => $isInside,
            ];
        }
        return $this->render('map/index.html.twig', [
            'pointsData' => $pointsData,
            'polygones' => $polygons,
        ]);
    }

    #[Route('/map/savePoint', name: 'point_save')]
    public function savePoint(Request $request,ManagerRegistry $mr): Response
    {
        $latitude = $request->request->get('latitude');
        $longitude = $request->request->get('longitude');
        $nom = $request->request->get('nom');

        if ($latitude && $longitude && $nom) {
            $points = new Pointer();
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
        $allPoints = $mr->getRepository(Pointer::class)->findAll();
        return $this->render('map/editMap.html.twig', [
            'allPoints' => $allPoints,
        ]);
    }
}
