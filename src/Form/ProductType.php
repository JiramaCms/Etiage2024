<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null,[
                'label' => 'Nom',
                'help' => 'Mettez le nom du produit',
                'attr' => [
                    'placeholder' => 'nom du produit'
                ]
            ])
            ->add('quantity')
            ->add('description')
            ->add('expiredAt',DateTimeType::class,[
                'input' => 'datetime_immutable',
                'widget' =>'single_text'
            ])
             ->add('classGroup',EntityType::class,[
                'placeholder' => 'Choisissez votre categorie',
                'class' => Categorie::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
               /* 'choice_attr' =>function($choice,$key,$value){
                    return $value == 1 ? ['disabled' => 'disabled'] : []; 
                }*/
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
