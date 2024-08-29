<?php

namespace App\Form;

use App\Entity\Action;
use App\Entity\Observation;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ObservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        
        $builder
            ->add('libelle')
            ->add('detail',TextareaType::class)
            ->add('dateHeure',DateTimeType::class,[
                'input' => 'datetime_immutable',
                'widget' =>'single_text'
            ])
        ;

        if (!$options['getIdByUrl']) {
            $builder->add('action',EntityType::class,[
                'placeholder' => 'Choisissez l\'action',
                'class' => Action::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.id','DESC');
                },
                'choice_label' => 'libelle',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Observation::class,
            'getIdByUrl' => false,
        ]);
    }
}
