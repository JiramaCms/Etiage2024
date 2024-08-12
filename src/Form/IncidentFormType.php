<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Incident;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class IncidentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('date', DateTimeType::class,[
                'input' => 'datetime_immutable',
                'widget' => 'single_text'
            ])
            ->add('description')
            ->add('site',EntityType::class,[
                'placeholder' => 'Choisissez votre site',
                'class' => Site::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.libelle','ASC');
                },
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Incident::class,
        ]);
    }
}
