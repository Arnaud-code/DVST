<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // $product = new Product();
        // $manager->persist($product);

        for ($p = 0; $p < 10; $p++) {
            $user = new User;

            $hash = $this->encoder->encodePassword($user, $faker->password());

            $user->setEmail($faker->email())
                ->setFullName($faker->name())
                ->setPassword($hash);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
