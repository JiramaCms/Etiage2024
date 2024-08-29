<?php

namespace App\Form;

use App\Entity\Materiel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MaterielFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('code')
            ->add('utilite', TextareaType::class, [
                'label' => 'Utilité',
                'required' => false, // Si vous voulez que ce champ soit facultatif
                'attr' => [
                    'placeholder' => 'Décrivez l\'utilité du matériel...',
                    'rows' => 5, // Vous pouvez ajuster le nombre de lignes par défaut
                ],
            ])
            ->add('capacite')
            ->add('action', HiddenType::class, [
                'label' => 'Action',
                'required' => false,
                'empty_data' => null, // Valeur par défaut
            ]);
        ;

        if($options['is_edit']){
            $builder->add('disposition',CheckboxType::class,[
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Materiel::class,
            'is_edit' => false,
        ]);
    }
}

