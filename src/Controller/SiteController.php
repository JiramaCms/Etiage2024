<?php

namespace App\Controller;

use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    #[Route('/site', name: 'app_site')]
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }
    
    #[Route('/site/insert', name: 'site_insert', methods: ['POST'])]
    public function insertPV(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupéreration des données du formulaire
        $libelle = $request->request->get('libelle');
        $adresse = $request->request->get('adresse');
        $coord = $request->request->get('coord');
        
        // Convertir les coordonnées en tableau [latitude, longitude]
        $coordArray = explode(',', str_replace(['LatLng(', ')'], '', $coord));
        $latitude = floatval($coordArray[0]);
        $longitude = floatval($coordArray[1]);

        // Créer une nouvelle instance de Site et définir ses propriétés
        $site = new Site();
        $site->setLibelle($libelle);
        $site->setAdresse($adresse);
        $site->setCoord(['latitude' => $latitude, 'longitude' => $longitude]);

        $entityManager->getRepository(site::class)->insert($site);


        // Rediriger vers
        return $this->redirectToRoute('app_site');
    }

    #[Route('/site/liste', name: 'site_liste')]
    public function site(EntityManagerInterface $entityManager): Response
    {
        $rsite =  $entityManager->getRepository(Site::class);
        $site = $rsite->findAll();

        return $this->render('site/site.html.twig', [
            'sites' => $site,
        ]);
    }
}
