<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PackageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name',TextType::class,[
            'label' => 'Name',
        ])
        ->add('description',TextareaType::class,[
            'label' => 'Description',
        ])
        ->add('product',EntityType::class,[
            'label' => 'Product',
            'class' => Product::class,
            'choice_label' => 'name',
            'placeholder' => 'Select a product', 
        ])
        ->add('price',NumberType::class,[
            'label' => 'price',
            'attr' =>  [
                'min' => 0,
                'step' => 0.01, 
            ],
         ]);
    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
