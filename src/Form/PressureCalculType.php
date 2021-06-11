<?php

namespace App\Form;

use App\Entity\PressureRecord;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PressureCalculType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('datetime')
            ->add('tempGround', NumberType::class, [
                'label' => 'Température au sol',
                'attr' => [
                    // 'class' => 'form-range',
                    'min' => -10,
                    'max' => 40,
                    'step' => 1,
                ]
            ])
            ->add('tempFrontLeft', NumberType::class, [
                'label' => 'Temp.',
                'attr' => [
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            ->add('tempFrontRight', NumberType::class, [
                'label' => 'Temp.',
                'attr' => [
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            ->add('tempRearLeft', NumberType::class, [
                'label' => 'Temp.',
                'attr' => [
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            ->add('tempRearRight', NumberType::class, [
                'label' => 'Temp.',
                'attr' => [
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            // ->add('user')
            // ->add('tire')
            // ->add('driver')
            // ->add('circuit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PressureRecord::class,
        ]);
    }
}
