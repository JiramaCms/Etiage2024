<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Objectif;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ObjectifFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('description')
            ->add('budget')
            ->add('deadline',DateType::class,[
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd'
            ])
            ->add('estimationCible')
            //->add('resultat')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Pas commencé' => 'inactif',
                    'En cours' => 'actif',
                    'Fini' => 'done',
                    'Abandonné' => 'abandonner'
                ],
                'placeholder' => 'Sélectionnez un statut',
            ])
            ->add('site',EntityType::class,[
                'placeholder' => 'Choisissez votre site',
                'class' => Site::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.libelle','ASC');
                },
                'choice_label' => 'libelle',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Objectif::class,
        ]);
    }
}
