<?php 

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Station;
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
    public function importData(Request $request, ManagerRegistry $mr): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');
            try {
                $filePath = $file->getPathname();
                $extension = $file->getClientOriginalExtension();

                // Charger le fichier en fonction de son type
                $rows = $this->loadFileData($filePath, $extension);
                //dd($rows); // Ajoutez cette ligne pour voir le contenu des lignes chargées

                $em = $mr->getManager();

                // Boucle sur les lignes pour insérer dans la base de données
                foreach ($rows as $index => $row) {
                    if ($index === 0) {
                        continue; // Ignorer la première ligne (l'en-tête)
                    }
                    $data = explode(';', $row[0]);

                    // Créer une nouvelle entité Production et assigner les valeurs
                    $production = new Production();
                    $production->setDaty(new \DateTime($data[0])); // Colonne date_p

                    $station = $em->getRepository(Station::class)->findOneBy(['code' => $data[1]]); // Colonne site_id correspond à 'code'
                    if (!$station) {
                        throw $this->createNotFoundException('Pas de station trouvé pour l\'ID ' . $data[1]);
                    }

                    $production->setStation($station);
                    $production->setQuantite((int)$data[2]); // Colonne qte_produite
                    //dd($production);
                    // Persister l'entité
                    $em->persist($production);
                }

                $em->flush(); // Sauvegarder toutes les entités persistées
                $this->addFlash('success', 'Les données ont été importées avec succès !');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
            }
            return $this->redirectToRoute('app_production');

        }

        return $this->render('production/import.html.twig');
    }

    private function loadFileData(string $filePath, string $extension): array
    {
        if ($extension === 'csv') {
            return array_map('str_getcsv', file($filePath, FILE_SKIP_EMPTY_LINES));
        }

        // Pour les fichiers Excel, on utilise PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        return $worksheet->toArray();
    }
    
}
