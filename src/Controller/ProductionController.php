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

            // Charger le fichier Excel
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            $em = $mr->getManager();
            

            // Boucle sur les lignes pour insérer dans la base de données
            foreach ($rows as $index => $row) {
                // Ignorer l'en-tête si nécessaire (index 0)
                if ($index === 0) {
                    continue;
                }
                // Créez une nouvelle entité Production et assignez les valeurs
                $production = new Production();
                $production->setDescription($row[0]); // Description
                $production->setQuantite((int)$row[1]); // Quantité
                $production->setDaty(new \DateTime($row[2])); // Date
                $production->setGap($row[3]); // Gap
                $id=($row[4]); // Site ID
                $site = $em->getRepository(Site::class)->find($id);
                if(!$site){
                    throw $this->createNotFoundException('Pas de Site trouvé');
                }
                $production->setSite($site);

                // Enregistrez l'entité
                $entityManager = $mr->getManager();
                $entityManager->persist($production);
            }

            $entityManager->flush();

            // Redirigez ou affichez un message de succès
            return $this->redirectToRoute('app_production'); // Ou une autre route de succès
        }

        return $this->render('import.html.twig'); // Créez un template pour le téléchargement
    }
    
}
