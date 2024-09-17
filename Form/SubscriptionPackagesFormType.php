<?php

namespace App\Form;

use App\Entity\Package;
use App\Entity\Subscription;
use App\Entity\PackageSubscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SubscriptionPackagesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('price', NumberType::class, [
                'label' => 'Price',
                   
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
            'data_class' => PackageSubscription::class,
        ]);
    }
}
