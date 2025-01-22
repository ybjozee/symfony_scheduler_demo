<?php

namespace App\MessageHandler;

use App\Entity\Incident;
use App\Entity\IncidentType;
use App\Entity\Worker;
use App\Message\GenerateIncidents;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateIncidentsHandler {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorkerRepository       $workerRepository,
    ) {
    }

    public function __invoke(GenerateIncidents $message)
    : void {

        $faker = Factory::create();
        $allWorkers = $this->workerRepository->findAll();
        $affectedWorkers = $faker->randomElements($allWorkers, $faker->numberBetween(1, count($allWorkers)), true);
        /**@var Worker $worker */
        foreach ($affectedWorkers as $worker) {
            $this->entityManager->persist(
                new Incident(
                    $faker->randomElement(IncidentType::cases()), $worker
                )
            );
        }
        $this->entityManager->flush();
    }
}
