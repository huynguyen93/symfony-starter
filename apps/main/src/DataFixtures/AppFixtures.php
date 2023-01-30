<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    const NB_USERS = 50;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::NB_USERS; $i++) {
            $user = new User();
            $user->setEmail('test@test.com'.$i);
            $user->setDisplayName($faker->name());
            $user->setPassword('1');

            $manager->persist($user);
        }

        $manager->flush();
    }
}
