<?php

namespace Fulll\Console\Commands\Memory;

use PHPUnit\Framework\TestCase;
use Fulll\Infra\Memory\InMemoryFleetRepository;
use Fulll\Infra\Memory\InMemoryVehicleRepository;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Fulll\Domain\Memory\Fleet;
use Fulll\Domain\Memory\User;
use Fulll\Domain\Memory\Vehicle;
use Symfony\Component\Console\Command\Command;
use Fulll\Console\Commands\Memory\RegisterVehicleCommand;

class RegisterVehicleCommandTest extends TestCase
{
    private $fleetRepository;
    private $vehicleRepository;
    private $registerVehicleCommand;

    protected function setUp(): void
    {
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->vehicleRepository = new InMemoryVehicleRepository();
        $this->registerVehicleCommand = new RegisterVehicleCommand($this->fleetRepository, $this->vehicleRepository);
    }

    public function testRunCommandFleetNotFound()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->registerVehicleCommand->runCommand($input, $output, 'non_existing_fleet', 'ABC123');

        $this->assertEquals(Command::FAILURE, $result);
        $this->assertStringContainsString('Fleet not found.', $output->fetch());
    }

    public function testRunCommandVehicleAlreadyRegistered()
    {
        $user = new User('user_1');
        $fleet = new Fleet('fleet_1', $user);
        $this->fleetRepository->save($fleet);

        $vehicle = new Vehicle('ABC123');
        $fleet->addVehicle($vehicle);
        $this->fleetRepository->save($fleet);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->registerVehicleCommand->runCommand($input, $output, 'fleet_1', 'ABC123');

        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertStringContainsString('This vehicle has already been registered into your fleet.', $output->fetch());
    }

    public function testRunCommandVehicleSuccessfullyRegistered()
    {
        $user = new User('user_1');
        $fleet = new Fleet('fleet_1', $user);
        $this->fleetRepository->save($fleet);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $result = $this->registerVehicleCommand->runCommand($input, $output, 'fleet_1', 'ABC123');

        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertStringContainsString('Vehicle registered successfully in the fleet.', $output->fetch());

        $fleet = $this->fleetRepository->getById('fleet_1');
        $this->assertCount(1, $fleet->getVehicles());
        $this->assertEquals('ABC123', $fleet->getVehicles()[0]->getPlateNumber());
    }
}