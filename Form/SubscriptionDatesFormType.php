<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionDatesFormType extends AbstractType
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
                'data' => 0.0, // Initialize Total_Price with 0.0
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
