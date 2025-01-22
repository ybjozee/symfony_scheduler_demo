<?php

namespace App\DataFixtures;

use App\Entity\Worker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    : void {

        $faker = Factory::create();
        for ($i = 0; $i < 100; $i++) {
            $manager->persist(
                new Worker($faker->name(), $faker->biasedNumberBetween(18, 50))
            );
        }
        $manager->flush();
    }
}

