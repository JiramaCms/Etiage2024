<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Source;
use App\Entity\Station;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('libelle')
            //->add('site')
            ->add('sources', EntityType::class, [ // Changed to sources
                'class' => Source::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                            ->orderBy('p.nom', 'ASC');
                },
                'choice_label' => 'nom',
                'multiple' => true,
            ]);
            if (!$options['getIdByUrl']) {
                $builder->add('site',EntityType::class,[
                    'placeholder' => 'Choisissez votre site',
                    'class' => Site::class,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.libelle','ASC');
                    },
                    'choice_label' => 'libelle',
                    ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Station::class,
            'getIdByUrl' => false,
        ]);
    }
}
