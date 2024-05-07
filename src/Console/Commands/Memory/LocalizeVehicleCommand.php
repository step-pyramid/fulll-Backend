<?php
namespace Fulll\Console\Commands\Memory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Infra\Memory\InMemoryVehicleRepository;
use Fulll\Infra\Memory\InMemoryFleetRepository;
use Fulll\Domain\Memory\User;
use Fulll\Domain\Memory\Fleet;
use Fulll\Domain\Memory\Vehicle;
use Fulll\Domain\Memory\Location;

class LocalizeVehicleCommand 
{
    private $vehicleRepository;
    private $fleetRepository;

    public function __construct(InMemoryFleetRepository $fleetRepository, InMemoryVehicleRepository $vehicleRepository)
    {
        $this->fleetRepository = $fleetRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function runCommand(InputInterface $input, OutputInterface $output, string $fleetId, string $plateNumber,
        string $latitude, string $longitude, ?string $altitude): int
    {
        $fleet = $this->fleetRepository->getById($fleetId);

        if (!$fleet) {
            $output->writeln('Fleet not found, please supply an existing fleet or 
                create a new fleet with command: create-fleet <userId>');
            return Command::FAILURE;
        }

        $vehicle = $fleet->getVehicleByPlateNumber($plateNumber);

        if (!$vehicle) {
            $output->writeln('Vehicle not found in the specified fleet. Please first register 
                vehicle to fleet using the command register-vehicle <fleetId> <plateNumber>');
            return Command::FAILURE;
        }

        $newLocation = new Location($latitude, $longitude, $altitude);
        $currentLocation = $vehicle->getLocation();
        if ($currentLocation && $currentLocation->isEqual($newLocation)) {
            $output->writeln('Cannot park at the same location two times in a row');
            return Command::FAILURE;
        }

        // Check if another vehicle is already parked at the specified location
        foreach ($this->vehicleRepository->getAllVehicles() as $v) {
            $currentLocation = $v->getLocation();
            if ($currentLocation && $currentLocation->isEqual($newLocation)) {
                $output->writeln('Cannot park multiple vehicles at the same location.');
                return Command::FAILURE;
            }
        }

        $vehicle->setLocation($newLocation);
        $this->vehicleRepository->save($vehicle); // Ensure the vehicle's new location is persisted

        $output->writeln('Vehicle location updated successfully.');

        return Command::SUCCESS;
    }
}