<?php
namespace Fulll\Console\Commands\Persistant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Infra\InMemoryUserRepository;
use Fulll\Domain\Persistant\Entities\Fleet;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Fulll\Domain\Persistant\Entities\User;
use Fulll\Domain\Persistant\Entities\Vehicle;

class RegisterVehicle
{

	private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function runCommand(InputInterface $input, OutputInterface $output, string $fleetId, string $plateNumber): int
    {

        $fleet = $this->entityManager->find(Fleet::class, $fleetId);

        // Check if the fleet entity exists
        if (!$fleet) {
            $output->writeln('Fleet not found.');
            return Command::FAILURE;
        }

        $vehicle = $this->entityManager->find(Vehicle::class, $plateNumber);

        if (!$vehicle) {
            $vehicle = new Vehicle($plateNumber);
            $this->entityManager->persist($vehicle); // Persist the new Vehicle object
        }
        
        // Check if the vehicle is already part of the fleet
        if ($fleet->getVehicles()->contains($vehicle)) {
            // If the vehicle is already in the fleet, inform the user
            $output->writeln("This vehicle has already been registered into your fleet.\n");
        } else {
            // If the vehicle is not in the fleet, add it to the fleet
            $fleet->addVehicle($vehicle);
            $this->entityManager->flush();
            $output->writeln('Vehicle registered successfully in the fleet.');
        }

        return Command::SUCCESS;
        
    }

}