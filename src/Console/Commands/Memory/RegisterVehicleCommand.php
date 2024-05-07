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

class RegisterVehicleCommand 
{
    private $vehicleRepository;
    private $fleetRepository;

    public function __construct(InMemoryFleetRepository $fleetRepository, InMemoryVehicleRepository $vehicleRepository)
    {
        $this->fleetRepository = $fleetRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function runCommand(InputInterface $input, OutputInterface $output, string $fleetId, string $plateNumber): int
    {
        $fleet = $this->fleetRepository->getById($fleetId);

        if (!$fleet) {
            $output->writeln('Fleet not found.');
            return Command::FAILURE;
        }

        $vehicle = $this->vehicleRepository->getByPlateNumber($plateNumber);

        if (!$vehicle) {
            $vehicle = new Vehicle($plateNumber);
            $this->vehicleRepository->save($vehicle);
        }

        if ($fleet->hasVehicle($vehicle)) {
            $output->writeln('This vehicle has already been registered into your fleet.');
        } else {
            $fleet->addVehicle($vehicle);
            $this->fleetRepository->save($fleet); // Update fleet with the new vehicle
            $output->writeln('Vehicle registered successfully in the fleet.');
        }
        return Command::SUCCESS;
    }
}