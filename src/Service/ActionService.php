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
     * Cette méthode retourne une liste de types à prendre en action pour satisfaire un objectif donné avec un budget.
     */
    public function bestActionToTake(Objectif $objectif): array
    {
        // Appelle la méthode du repository pour récupérer les types qui satisfont les critères.
        $allMateriel = $this->typeRepository->findAll();
        $types = []; // Initialise un tableau vide pour stocker les types sélectionnés

        if (count($allMateriel) > 0) {
            // Initialise le coût minimum avec un coût élevé pour le comparer avec les coûts réels
            $minCout = PHP_INT_MAX; 

            // Parcours de tous les matériels pour trouver le coût minimum
            foreach ($allMateriel as $materiel) {
                if ($materiel->getCout() !== null && $materiel->getCout() < $minCout) {
                    $minCout = $materiel->getCout();
                }
            }

            // Deuxième boucle pour ajouter tous les matériels avec le coût minimum au tableau $types
            foreach ($allMateriel as $materiel) {
                if ($materiel->getCout() !== null && $materiel->getCout() == $minCout) {
                    $types[] = $materiel;
                }
            }
        return $allMateriel;
        }
    }

}
