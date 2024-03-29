<?php
namespace Tests\Persistant;

use Fulll\Console\Commands\Persistant\LocalizeVehicle;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Domain\Persistant\Entities\Fleet;
use Fulll\Domain\Persistant\Entities\Vehicle;
use Fulll\Domain\Persistant\Entities\Location;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Command\Command;  

class LocalizeVehicleTest extends TestCase
{
    protected $entityManager;
    protected $input;
    protected $output;
    protected $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);

        // Mock the repository to return the mocked repository when getRepository is called
        $this->entityManager->method('getRepository')->willReturn($this->repository);
    }

    public function testRunCommandFleetNotFound()
    {
        $fleetId = 'non-existing-fleet-id';
        $plateNumber = 'plateNumber';
        $latitude = '10';
        $longitude = '20';
        $altitude = '30';

        $this->entityManager->expects($this->once())
            ->method('find')
            ->with(Fleet::class, $fleetId)
            ->willReturn(null);

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('Fleet not found.'));

        $command = new LocalizeVehicle($this->entityManager);
        $result = $command->runCommand($this->input, $this->output, $fleetId, $plateNumber, $latitude, $longitude, $altitude);

        $this->assertEquals(Command::FAILURE, $result);

    }

    public function testRunCommandVehicleNotFound()
    {
        $fleetId = 'existing-fleet-id';
        $plateNumber = 'non-existing-plateNumber';
        $latitude = '10';
        $longitude = '20';
        $altitude = '30';

        $fleet = $this->createMock(Fleet::class);

        $this->entityManager->expects($this->any())
            ->method('find')
            ->willReturnMap([
                [Fleet::class, $fleetId, $fleet],  // Return $fleet when finding Fleet with $fleetId
                [Vehicle::class, $plateNumber, null],  // Return null when finding Vehicle with $plateNumber
            ]);

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('Vehicle not found.'));

        $command = new LocalizeVehicle($this->entityManager);
        $result = $command->runCommand($this->input, $this->output, $fleetId, $plateNumber, $latitude, $longitude, $altitude);

        $this->assertEquals(Command::FAILURE, $result);
 
    }

    public function testRunCommandVehicleAlreadyParked()
    {
        $fleetId = 'existing-fleet-id';
        $plateNumber = 'existing-plateNumber';
        $latitude = '10';
        $longitude = '20';
        $altitude = '30';

        $fleet = $this->createMock(Fleet::class);
        $vehicle = $this->createMock(Vehicle::class);
        $location = $this->createMock(Location::class);

        $this->entityManager->expects($this->any())
            ->method('find')
            ->willReturnMap([
                [Fleet::class, $fleetId, $fleet],  // Return $fleet when finding Fleet with $fleetId
                [Vehicle::class, $plateNumber, $vehicle],  // Return $vehicle when finding Vehicle with $plateNumber
            ]);


        $fleet->expects($this->once())
            ->method('getVehicles')
            ->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$vehicle]));

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['latitude' => $latitude, 'longitude' => $longitude, 'altitude' => $altitude])
            ->willReturn($location);

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('Vehicle already parked at this location'));

        $location->expects($this->once())
            ->method('getVehicle')
            ->willReturn($vehicle);

        $command = new LocalizeVehicle($this->entityManager);
        $result = $command->runCommand($this->input, $this->output, $fleetId, $plateNumber, $latitude, $longitude, $altitude);

        $this->assertEquals(Command::FAILURE, $result);

    }

    public function testRunCommandVehicleSuccessfullyLocalized()
    {
        $fleetId = 'existing-fleet-id';
        $plateNumber = 'existing-plateNumber';
        $latitude = '10';
        $longitude = '20';
        $altitude = '30';

        $fleet = $this->createMock(Fleet::class);
        $vehicle = $this->createMock(Vehicle::class);
        $location = null;

        $this->entityManager->expects($this->any())
            ->method('find')
            ->willReturnMap([
                [Fleet::class, $fleetId, $fleet],  // Return $fleet when finding Fleet with $fleetId
                [Vehicle::class, $plateNumber, $vehicle],  // Return $vehicle when finding Vehicle with $plateNumber
            ]);

        $fleet->expects($this->once())
            ->method('getVehicles')
            ->willReturn(new \Doctrine\Common\Collections\ArrayCollection([$vehicle]));

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['latitude' => $latitude, 'longitude' => $longitude, 'altitude' => $altitude])
            ->willReturn($location);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Location::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('Vehicle parked successfully.'));

        $vehicle->expects($this->once())
            ->method('setLocation')
            ->with($this->isInstanceOf(Location::class));

        $command = new LocalizeVehicle($this->entityManager);
        $result = $command->runCommand($this->input, $this->output, $fleetId, $plateNumber, $latitude, $longitude, $altitude);

        $this->assertEquals(Command::SUCCESS, $result);

    }
}
