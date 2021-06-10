<?php

namespace App\Form;

use App\Entity\Circuit;
use App\Entity\Driver;
use App\Entity\PressureRecord;
use App\Entity\Tire;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PressureConditionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('datetime')
            // ->add('tempGround')
            // ->add('tempFrontLeft')
            // ->add('tempFrontRight')
            // ->add('tempRearLeft')
            // ->add('tempRearRight')
            // ->add('pressFrontLeft')
            // ->add('pressFrontRight')
            // ->add('pressRearLeft')
            // ->add('pressRearRight')
            // ->add('user')
            ->add('tire', EntityType::class, [
                'label' => 'Pneus',
                'placeholder' => '-- Choisir les pneus --',
                'class' => Tire::class,
                'choice_label' => function (Tire $tire) {
                    return strtoupper($tire->getName());
                }
            ])
            ->add('driver', EntityType::class, [
                'label' => 'Pilote',
                'placeholder' => '-- Choisir le pilote --',
                'class' => Driver::class,
                'choice_label' => function (Driver $driver) {
                    return strtoupper($driver->getName());
                }
            ])
            ->add('circuit', EntityType::class, [
                'label' => 'Circuit',
                'placeholder' => '-- Choisir le circuit --',
                'class' => Circuit::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('query')
                        ->orderBy('query.name', 'ASC');
                },
                'choice_label' => function (Circuit $circuit) {
                    return strtoupper($circuit->getName());
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PressureRecord::class,
        ]);
    }
}
