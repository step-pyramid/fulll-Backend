<?php

namespace Fulll\Console\Commands\Memory;

use PHPUnit\Framework\TestCase;
use Fulll\Infra\Memory\InMemoryVehicleRepository;
use Fulll\Infra\Memory\InMemoryFleetRepository;
use Fulll\Domain\Memory\User;
use Fulll\Domain\Memory\Fleet;
use Fulll\Domain\Memory\Vehicle;
use Fulll\Domain\Memory\Location;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Command\Command;

class LocalizeVehicleCommandTest extends TestCase
{
    private $fleetRepository;
    private $vehicleRepository;
    private $localizeVehicleCommand;

    protected function setUp(): void
    {
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->vehicleRepository = new InMemoryVehicleRepository();
        $this->localizeVehicleCommand = new LocalizeVehicleCommand($this->fleetRepository, $this->vehicleRepository);
    }

    public function testRunCommandFleetNotFound()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->localizeVehicleCommand->runCommand($input, $output, 'non_existing_fleet', 'ABC123', '12.34', '56.78', null);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Fleet not found', $output->fetch());
    }

    public function testRunCommandVehicleNotFoundInFleet()
    {
        $user = new User('user_1');
        $fleet = new Fleet('fleet_1', $user);
        $this->fleetRepository->save($fleet);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->localizeVehicleCommand->runCommand($input, $output, 'fleet_1', 'non_existing_plate', '12.34', '56.78', null);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Vehicle not found in the specified fleet', $output->fetch());
    }

    public function testRunCommandCannotParkAtSameLocationTwice()
    {
        $user = new User('user_1');
        $fleet = new Fleet('fleet_1', $user);
        $this->fleetRepository->save($fleet);

        $vehicle = new Vehicle('ABC123');
        $location = new Location('12.34', '56.78', null);
        $vehicle->setLocation($location);
        $fleet->addVehicle($vehicle);
        $this->fleetRepository->save($fleet);
        $this->vehicleRepository->save($vehicle);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->localizeVehicleCommand->runCommand($input, $output, 'fleet_1', 'ABC123', '12.34', '56.78', null);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Cannot park at the same location two times in a row', $output->fetch());
    }

    public function testRunCommandCannotParkMultipleVehiclesAtSameLocation()
    {
        $user = new User('user_1');
        $fleet1 = new Fleet('fleet_1', $user);
        $this->fleetRepository->save($fleet1);

        $vehicle1 = new Vehicle('ABC123');
        $location = new Location('12.34', '56.78', null);
        $vehicle1->setLocation($location);
        $fleet1->addVehicle($vehicle1);
        $this->fleetRepository->save($fleet1);
        $this->vehicleRepository->save($vehicle1);

        $fleet2 = new Fleet('fleet_2', $user);
        $this->fleetRepository->save($fleet2);

        $vehicle2 = new Vehicle('DEF456');
        $fleet2->addVehicle($vehicle2);
        $this->fleetRepository->save($fleet2);
        $this->vehicleRepository->save($vehicle2);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->localizeVehicleCommand->runCommand($input, $output, 'fleet_2', 'DEF456', '12.34', '56.78', null);

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Cannot park multiple vehicles at the same location.', $output->fetch());
    }

    public function testRunCommandVehicleSuccessfullyLocalized()
    {
        $user = new User('user_1');
        $fleet = new Fleet('fleet_1', $user);
        $this->fleetRepository->save($fleet);

        $vehicle = new Vehicle('ABC123');
        $fleet->addVehicle($vehicle);
        $this->fleetRepository->save($fleet);
        $this->vehicleRepository->save($vehicle);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->localizeVehicleCommand->runCommand($input, $output, 'fleet_1', 'ABC123', '12.34', '56.78', '100');

        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertStringContainsString('Vehicle location updated successfully.', $output->fetch());

        $vehicle = $this->vehicleRepository->getByPlateNumber('ABC123');
        $this->assertNotNull($vehicle->getLocation());
        $this->assertEquals('12.34', $vehicle->getLocation()->getLatitude());
        $this->assertEquals('56.78', $vehicle->getLocation()->getLongitude());
        $this->assertEquals('100', $vehicle->getLocation()->getAltitude());
    }
}
