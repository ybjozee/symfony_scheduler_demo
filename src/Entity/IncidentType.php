<?php

namespace App\Entity;

use Faker\Factory;

enum IncidentType: string {

    case HARMFUL_EXPOSURE = 'Harmful exposure';
    case MACHINE_FAULT = 'Machine Fault';
    case BURN = 'Burn';
    case NEGLIGENT_MANAGEMENT = 'Negligent management';

    public function getCompensationDue()
    : float {

        $faker = Factory::create();
        $fine = match ($this) {
            self::HARMFUL_EXPOSURE => 100000,
            self::MACHINE_FAULT => 10000,
            self::BURN => 50000,
            self::NEGLIGENT_MANAGEMENT => 1000000,
        };

        return $faker->randomFloat(null, 0.6, 4) * $fine;
    }
}

