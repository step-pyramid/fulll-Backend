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
use Fulll\Domain\Persistant\Entities\Location;

class LocalizeVehicle
{

	private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function runCommand(InputInterface $input, OutputInterface $output, string $fleetId, string $plateNumber, 
        string $latitude, string $longitude, ?string $altitude): int
    {

        $fleet = $this->entityManager->find(Fleet::class, $fleetId);

        // Check if the fleet entity exists
        if (!$fleet) {
            $output->writeln('Fleet not found. Please first register your fleet with the following command: create-fleet <userid>');
            return Command::FAILURE;
        }

        $vehicle = $this->entityManager->find(Vehicle::class, $plateNumber);

        if (!$vehicle) {
            $output->writeln('Vehicle not found. Please first register your vehicle to the fleet with the 
                command: register-vehicle <fleetId> <vehiclePlateNumber>');
            return Command::FAILURE;
        }

        // Check if the vehicle is already part of the fleet
        if ($fleet->getVehicles()->contains($vehicle)) {

            $location = $this->entityManager->getRepository(Location::class)->findOneBy([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'altitude' => $altitude
            ]);

            if ($location) {
                // Check if there is any other vehicle associated with the same location
                $existingVehicle = $location->getVehicle();
                if ($existingVehicle){
                    if($existingVehicle !== $vehicle) {
                        $output->writeln('Cannot park multiple vehicles at the same location.');
                        return Command::FAILURE;
                    } else {
                        $output->writeln('Vehicle already parked at this location');
                        return Command::FAILURE;
                    }
                }
            } else {
                // If the vehicle is already in the fleet
                $location = new Location($latitude, $longitude, $altitude);
                $this->entityManager->persist($location); // Persist the new Location object
            }

            // Set the location for the vehicle
            $vehicle->setLocation($location);
            $this->entityManager->flush();

            $output->writeln('Vehicle parked successfully.');
            
            return Command::SUCCESS;



        } else {
            // If the vehicle is not in the fleet, add it to the fleet
            $output->writeln('Please first register this Vehicle to your fleet with the appropriate command.');
            return Command::FAILURE;
        }
        
    }

}