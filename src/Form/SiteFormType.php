<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Source;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('adresse')
            ->add('latitude', null, [
                'mapped' => false, // Prevents mapping to an entity field
                'data' => $options['data']->getLatitude(), // Pre-populate with the current latitude
                'label' => 'Latitude'
            ])
            ->add('longitude', null, [
                'mapped' => false, // Prevents mapping to an entity field
                'data' => $options['data']->getLongitude(), // Pre-populate with the current longitude
                'label' => 'Longitude'
            ])
            ->add('code')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Site::class,
        ]);
    }
}
