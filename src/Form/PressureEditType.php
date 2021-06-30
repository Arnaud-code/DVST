<?php

namespace App\Form;

use App\Entity\PressureRecord;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PressureEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('datetime')
            ->add('tempTrack', NumberType::class, [
                // 'label' => 'Température piste',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    // 'class' => 'form-range',
                    'min' => -10,
                    'max' => 40,
                    'step' => 1,
                ]
            ])
            ->add('tempFrontLeft', NumberType::class, [
                // 'label' => 'Temp.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            ->add('tempFrontRight', NumberType::class, [
                // 'label' => 'Temp.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            ->add('tempRearLeft', NumberType::class, [
                // 'label' => 'Temp.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            ->add('tempRearRight', NumberType::class, [
                // 'label' => 'Temp.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 40,
                    'max' => 90,
                    'step' => 1,
                ]
            ])
            ->add('pressFrontLeft', NumberType::class, [
                // 'label' => 'Press.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 2.5,
                    'step' => 0.01,
                ]
            ])
            ->add('pressFrontRight', NumberType::class, [
                // 'label' => 'Press.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 2.5,
                    'step' => 0.01,
                ]
            ])
            ->add('pressRearLeft', NumberType::class, [
                // 'label' => 'Press.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 2.5,
                    'step' => 0.01,
                ]
            ])
            ->add('pressRearRight', NumberType::class, [
                // 'label' => 'Press.',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 2.5,
                    'step' => 0.01,
                ]
            ])
            ->add('note', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'class' => 'form-control',
                    'required' => false,
                    'placeholder' => "Saisissez votre commentaire ici",
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
