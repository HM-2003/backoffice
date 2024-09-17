<?php

namespace App\Form;

use App\Entity\Subscription;
use App\Entity\Package;
use App\Entity\PackageSubscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;


class SubscriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Date_Debut',DateType::class,[
                'label' => 'Date_Debut',
            ])
            ->add('Date_Fin',DateType::class,[
                'label' => 'Date_Fin',
            ])
            ->add('Total_Price',NumberType::class,[
                'label' => 'Total_Price',
                'attr' =>[
                    'min' => 0,
                    'step' => 0.01,
                ],
               
            ])
            
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'mapped' => false,    
            ])

            ->add('packages', EntityType::class, [
                'label' => 'packages',
                'class' => Package::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a package',
                'mapped' => false,
                'required' => false,
                //'multiple' => true,
                //'expanded' => true, //check boxes instead of multiple selection box 
            ]);
        
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here

            'data_class' => Subscription::class,
        ]);
    }
}
