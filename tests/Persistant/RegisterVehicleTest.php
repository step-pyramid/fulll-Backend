<?php

namespace Tests\Persistant;

use Fulll\Console\Commands\CreateFleet;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Domain\Persistant\Entities\User;
use Symfony\Component\Console\Command\Command;	
use Fulll\Domain\Persistant\Entities\Fleet;
use Fulll\Domain\Persistant\Entities\Vehicle;
use Fulll\Console\Commands\Persistant\RegisterVehicle;

class RegisterVehicleTest extends TestCase
{
    protected $entityManager;
    protected $input;
    protected $output;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
    }

    public function testRunCommandFleetNotFound()
    {

        $fleetId = 'non-existing-fleet-id';
        $plateNumber = 'plateNumber';

        // Configure the entity manager mock to return null when finding the fleet
        $this->entityManager->expects($this->once())
            ->method('find')
            ->with(Fleet::class, $fleetId)
            ->willReturn(null);

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('Fleet not found'));

        // Create an instance of RegisterVehicle with mocked entity manager
        $command = new RegisterVehicle($this->entityManager);

        // Execute the command
        $result = $command->runCommand($this->input, $this->output, $fleetId, $plateNumber);

        // Assert the output and result
        $this->assertEquals(Command::FAILURE, $result);

    }

    public function testRunCommandVehicleAlreadyRegistered()
    {
        $fleetId = 'existing-fleet-id';
        $plateNumber = 'plateNumber';

        // Create mock fleet and vehicle
        $fleet = $this->createMock(Fleet::class);
        $vehicle = $this->createMock(Vehicle::class);

        // Configure the entity manager mock to return fleet and vehicle
        $this->entityManager->expects($this->any())
            ->method('find')
            ->willReturnMap([
                [Fleet::class, $fleetId, $fleet],  // Return $fleet when finding Fleet with $fleetId
                [Vehicle::class, $plateNumber, $vehicle],  // Return $vehicle when finding Vehicle with $plateNumber
            ]);

        // Configure the fleet mock to contain the vehicle
        $fleet->expects($this->any())
            ->method('getVehicles')
            ->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$vehicle]));

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('This vehicle has already been registered into your fleet'));

        // Create an instance of RegisterVehicle with mocked entity manager
        $command = new RegisterVehicle($this->entityManager);

        // Execute the command
        $result = $command->runCommand($this->input, $this->output, $fleetId, $plateNumber);

        // Assert the output and result
        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testRunCommandVehicleRegisteredSuccessfully()
    {
        $fleetId = 'existing-fleet-id';
        $plateNumber = 'new-plateNumber';

        // Create mock fleet and vehicle
        $fleet = $this->createMock(Fleet::class);
        $vehicle = $this->createMock(Vehicle::class);

        // Configure the entity manager mock to return fleet and null for vehicle
        $this->entityManager->expects($this->any())
            ->method('find')
            ->willReturnMap([
                [Fleet::class, $fleetId, $fleet],  // Return $fleet when finding Fleet with $fleetId
                [Vehicle::class, $plateNumber, null],  // Return null when finding Vehicle with $plateNumber
            ]);

        // Configure the fleet mock to not contain the vehicle
        $fleet->expects($this->any())
            ->method('getVehicles')
            ->willReturn(new \Doctrine\Common\Collections\ArrayCollection());

        // Expect the entity manager to persist and flush the vehicle
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Vehicle::class));  // Expect persist to be called with an instance of Vehicle
        $this->entityManager->expects($this->once())
            ->method('flush');  // Expect flush to be called once

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('Vehicle registered successfully in the fleet'));

        // Create an instance of RegisterVehicle with mocked entity manager
        $command = new RegisterVehicle($this->entityManager);

        // Execute the command
        $result = $command->runCommand($this->input, $this->output, $fleetId, $plateNumber);

        // Assert the output and result
        $this->assertEquals(Command::SUCCESS, $result);
    }
}
