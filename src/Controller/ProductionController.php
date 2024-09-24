<?php 

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Besoin;
use App\Entity\Production;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductionController extends AbstractController
{
    #[Route('/production', name: 'app_production')]
    public function index(ManagerRegistry $mr): Response
    {
        return $this->render('production/index.html.twig', []);
    }

    #[Route('/import', name: 'import_production')]
    public function importExcel(Request $request, ManagerRegistry $mr): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');

            try {
                // Charger le fichier Excel
                $spreadsheet = IOFactory::load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                $em = $mr->getManager(); // Récupérer l'EntityManager

                // Boucle sur les lignes pour insérer dans la base de données
                foreach ($rows as $index => $row) {
                    if ($index === 0) {
                        continue; // Ignorer la première ligne (l'en-tête)
                    }

                    // Chaque ligne est une chaîne avec des valeurs séparées par un point-virgule
                    $data = explode(';', $row[0]);

                    // Créer une nouvelle entité Production et assigner les valeurs
                    $production = new Production();
                    $production->setDaty(new \DateTime($data[0]));
                    $site = $em->getRepository(Site::class)->find($data[1]); // Site ID

                    // Vérification du site
                    if (!$site) {
                        throw $this->createNotFoundException('Pas de Site trouvé pour ID ' . $data[1]);
                    }

                    $production->setSite($site);
                    $production->setQuantite((int)$data[2]); // Quantité

                    // Persister l'entité
                    $em->persist($production);
                }

                $em->flush(); // Sauvegarder toutes les entités persistées

                // Ajouter un message flash de succès
                $this->addFlash('success', 'Les données ont été importées avec succès !');
                
            } catch (\Exception $e) {
                // Ajouter un message flash d'erreur
                $this->addFlash('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
            }

            // Rediriger après l'importation
            return $this->redirectToRoute('app_production');
        }

        return $this->render('production/import.html.twig');
    }
    
}
