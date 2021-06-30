<?php

namespace App\Form;

use App\Entity\Circuit;
use App\Entity\Driver;
use App\Entity\PressureRecord;
use App\Entity\Tire;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PressureCombinationType extends AbstractType
{
    protected $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->user = $token->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('datetime')
            // ->add('tempTrack')
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
                // 'label' => 'Pneu',
                'label' => false,
                'placeholder' => '-- Choisir les pneus --',
                'class' => Tire::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('query')
                        ->where('query.user = :user')
                        ->setParameter('user', $this->user)
                        ->orderBy('query.name', 'ASC');
                },
                'choice_label' => function (Tire $tire) {
                    return strtoupper($tire->getName());
                },
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('driver', EntityType::class, [
                // 'label' => 'Pilote',
                'label' => false,
                'placeholder' => '-- Choisir le pilote --',
                'class' => Driver::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('query')
                        ->where('query.user = :user')
                        ->setParameter('user', $this->user)
                        ->orderBy('query.name', 'ASC');
                },
                'choice_label' => function (Driver $driver) {
                    return strtoupper($driver->getName());
                },
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('circuit', EntityType::class, [
                // 'label' => 'Circuit',
                'label' => false,
                'placeholder' => '-- Choisir le circuit --',
                'class' => Circuit::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('query')
                        ->where('query.user = :user')
                        ->setParameter('user', $this->user)
                        ->orderBy('query.name', 'ASC');
                },
                'choice_label' => function (Circuit $circuit) {
                    return strtoupper($circuit->getName());
                },
                'attr' => [
                    'class' => 'form-control',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PressureRecord::class,
        ]);
    }
}
