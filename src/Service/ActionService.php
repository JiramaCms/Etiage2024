<?php

namespace App\Service;

use App\Entity\Objectif;
use App\Repository\TypeRepository;

class ActionService
{
    private $typeRepository;

    public function __construct(TypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    /**
     * Cette méthode retourne une liste de types à prendre en action pour satisfaire un objectif donné.
     */
    public function bestActionToTake(Objectif $objectif): array
    {
        // Appelle la méthode du repository pour récupérer les types disponible.
        $allMateriel = $this->typeRepository->findAll();
        $budget = $objectif->getBudget();
        $capaciteCible = $objectif->getEstimationCible();
        $capacite = 0;
        $cout = 0;
        $tempCapacite = 0;
        $tempCout = 0;
        $types = [];
    
        if (count($allMateriel) > 0) {
            // Trier les matériels par coût croissant
            usort($allMateriel, function($a, $b) {
                return $b->getCapacite() <=> $a->getCapacite();
            });
    
            foreach ($allMateriel as $materiel) {
                $tempTypes[] = $materiel;
                $tempCapacite = $tempCapacite + $materiel->getCapacite(); 
                $tempCout = $tempCout + $materiel->getCout();
                $repTemp =[
                    'types' => $tempTypes,
                    'capacite' => $tempCapacite,
                    'cout' => $tempCout
                ];

                if($tempCapacite > $capaciteCible && $tempCout <= $budget ) return $repTemp;

                if($tempCapacite == $capaciteCible && $tempCout <= $budget) return $repTemp;

            }

            $tempTypes = [];
            $tempCapacite = 0;
            $tempCout = 0;


            usort($allMateriel, function($a, $b) {
                return $a->getCout() <=> $b->getCout();
            });
    
            foreach ($allMateriel as $materiel) {

                $tempTypes[] = $materiel;
                $tempCapacite = $tempCapacite + $materiel->getCapacite(); 
                $tempCout = $tempCout + $materiel->getCout();
                $repTemp =[
                    'types' => $tempTypes,
                    'capacite' => $tempCapacite,
                    'cout' => $tempCout
                ];
                //dump($tempCout);die();

                if($tempCout > $budget) break ; //return $types;

                if($tempCapacite > $capaciteCible) return $repTemp;

                if($tempCout == $budget) return $repTemp;

                if($tempCapacite == $capaciteCible) return $repTemp;

                $types [] = $materiel;
                $capacite = $capacite + $materiel->getCapacite(); 
                $cout = $cout + $materiel->getCout();

            }
        }
    
        return [
            'types' => $types,
            'capacite' => $capacite,
            'cout' => $cout
        ];
    }
    

}
