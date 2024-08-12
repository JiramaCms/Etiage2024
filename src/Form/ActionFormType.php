<?php

namespace App\Form;

use App\Entity\Action;
use App\Entity\Objectif;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ActionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('dateDebut',DateType::class,[
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd'
            ])
            // ->add('dateFin',DateType::class,[
            //     'widget' => 'single_text',
            //     'format' => 'yyyy-MM-dd'
            // ])
            ->add('avancement')
            ->add('objectif',EntityType::class,[
                'placeholder' => 'Choisissez l\'objectif en question',
                'class' => Objectif::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.id','DESC');
                },
                'choice_label' => 'libelle',
            ])
        ;

        // Ajout conditionnel du champ 'dateFin'
        if ($options['include_date_fin']) {
            $builder->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Action::class,
            'include_date_fin' => false, // Par défaut, 'dateFin' n'est pas inclus
        ]);
    }
}
