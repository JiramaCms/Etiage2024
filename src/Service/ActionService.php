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
    public function ActionToTake(Objectif $objectif): array
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
                $materielCout = $materiel->getCout();
                $materielCapacite = $materiel->getCapacite();
                $tempCapacite = $tempCapacite + $materiel->getCapacite(); 
                $tempCout = $tempCout + $materiel->getCout();
                $repTemp =[
                    'types' => $tempTypes,
                    'capacite' => $tempCapacite,
                    'cout' => $tempCout
                ];

                if($materielCapacite > $capaciteCible && $materielCout <= $budget ) {

                    return $rep = [
                        'types' => $materiel,
                        'capacite' => $materielCapacite,
                        'cout' => $materielCout
                    ];
                }

                if($materielCapacite == $capaciteCible && $materielCout <= $budget) return $repTemp;

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

    // Utilisation knapsack-problem solution mais modifier un peu
    public function bestActionToTake($budget, $capacite_cible) {

        $allMateriel = $this->typeRepository->findAll();
        usort($allMateriel, function($a, $b) {
            return $a->getCout() <=> $b->getCout();
        });
    
        // Variables pour stocker la meilleure combinaison trouvée
        $best_combination = [];
        $min_cost = PHP_INT_MAX;
        $best_capacity = 0;
        $max_capacity_combination = []; // Pour stocker la combinaison avec la capacité maximale
        $max_capacity = 0; // Pour stocker la capacité maximale trouvée
    
        // Fonction récursive pour explorer toutes les combinaisons possibles
        function findCombination($current_combination, $current_cost, $current_capacity, $index, $allMateriel, $budget, $capacite_cible, &$best_combination, &$min_cost, &$best_capacity, &$max_capacity_combination, &$max_capacity) {
            // Vérifier si la capacité cible est atteinte ou dépassée avec un coût minimal
            if ($current_capacity >= $capacite_cible) {
                if ($current_cost < $min_cost) {
                    $best_combination = $current_combination;
                    $min_cost = $current_cost;
                    $best_capacity = $current_capacity;
                }
            } else {
                // Si la capacité cible n'est pas atteinte, trouver la combinaison avec la capacité maximale sans dépasser le budget
                if ($current_capacity > $max_capacity) {
                    $max_capacity_combination = $current_combination;
                    $max_capacity = $current_capacity;
                }
            }
    
            // Retourner si l'index dépasse la taille de l'ensemble des matériaux
            if ($index >= count($allMateriel)) {
                return;
            }
    
            // Explorer l'ajout du matériau actuel
            $materiel = $allMateriel[$index];
            $new_cost = $current_cost + $materiel->getCout();
    
            // Si le nouveau coût ne dépasse pas le budget, explorer cette branche
            if ($new_cost <= $budget) {
                findCombination(
                    array_merge($current_combination, [$materiel]),
                    $new_cost,
                    $current_capacity + $materiel->getCapacite(),
                    $index + 1,
                    $allMateriel,
                    $budget,
                    $capacite_cible,
                    $best_combination,
                    $min_cost,
                    $best_capacity,
                    $max_capacity_combination,
                    $max_capacity
                );
            }
    
            // Explorer sans ajouter le matériau actuel
            findCombination(
                $current_combination,
                $current_cost,
                $current_capacity,
                $index + 1,
                $allMateriel,
                $budget,
                $capacite_cible,
                $best_combination,
                $min_cost,
                $best_capacity,
                $max_capacity_combination,
                $max_capacity
            );
        }
    
        // Appel initial de la fonction récursive
        findCombination([], 0, 0, 0, $allMateriel, $budget, $capacite_cible, $best_combination, $min_cost, $best_capacity, $max_capacity_combination, $max_capacity);
    
        // Si la capacité cible n'est pas atteinte, retourner la combinaison avec la capacité maximale
        if ($best_capacity < $capacite_cible) {
            $best_combination = $max_capacity_combination;
            $min_cost = array_reduce($max_capacity_combination, function($carry, $item) {
                return $carry + $item->getCout();
            }, 0);
            $best_capacity = $max_capacity;
        }
    
        // Retourne la meilleure combinaison avec son coût total et sa capacité totale
        return [
            'types' => $best_combination,
            'capacite' => $best_capacity,
            'cout' => $min_cost
        ];
    }
    
    
}
