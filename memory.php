<?php

require __DIR__.'/vendor/autoload.php';

use Fulll\Console\Commands\Memory\CreateFleetCommand;
use Fulll\Console\Commands\Memory\CreateUserCommand;
use Fulll\Console\Commands\Memory\RegisterVehicleCommand;
use Fulll\Console\Commands\Memory\LocalizeVehicleCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Command\Command;
use Fulll\Infra\Memory\InMemoryUserRepository;
use Fulll\Infra\Memory\InMemoryFleetRepository;
use Fulll\Infra\Memory\InMemoryVehicleRepository;
use Fulll\Domain\Memory\User;
use Fulll\Domain\Memory\Fleet;

// Function to handle command results
function handleCommandResult(int $result): void
{
    if ($result === Command::SUCCESS) {
        echo "Command executed successfully.\n";
    } else {
        echo "Command execution failed.\n";
    }
}

// Set up the global classes to implement memory storage
$userRepository = new InMemoryUserRepository();
$fleetRepository = new InMemoryFleetRepository();
$vehicleRepository = new InMemoryVehicleRepository();

// Main interactive loop
while (true) {
    echo "Enter command (type 'end' to quit):\n";
    $inputLine = trim(fgets(STDIN));

    if ($inputLine === 'end') {
        echo "Exiting...\n";
        break;
    }

    $inputParts = explode(' ', $inputLine);
    $commandName = $inputParts[0];
    $arguments = array_slice($inputParts, 1);

    switch ($commandName) {
        case 'create-user':
            if (count($arguments) > 0) {
            echo "Error: 'create-user' command does not take any arguments.\n";
            break;
            }
            $command = new CreateUserCommand($userRepository, $fleetRepository);
            $input = new ArrayInput([]);
            $output = new StreamOutput(STDOUT);
            $command->runCommand($input, $output);
            break;
        case 'create-fleet':
            if (count($arguments) < 1) {
                echo "Please enter a valid <userId>\n";
                break;
            }
            
            if (count($arguments) > 1) {
                echo "Error: 'create-fleet' command takes only one argument: <userId>\n";
                break;
            }
            $userId = $arguments[0];
            $command = new CreateFleetCommand($userRepository, $fleetRepository);
            $input = new ArrayInput([]);
            $output = new StreamOutput(STDOUT);
            $command->runCommand($input, $output, $userId);
            break;
        case 'register-vehicle':
            if (count($arguments) < 2) {
                echo "Please enter a valid <fleetId> and <vehiclePlateNumber>\n";
                break;
            }
            if (count($arguments) > 2) {
                echo "Error: 'register-vehicle' command takes only two arguments: <fleetId> and <vehiclePlateNumber>\n";
                break;
            }
            $fleetId = $arguments[0];
            $plateNumber = $arguments[1];
            $command = new RegisterVehicleCommand($fleetRepository, $vehicleRepository);
            $input = new ArrayInput([]);
            $output = new StreamOutput(STDOUT);
            $command->runCommand($input, $output, $fleetId, $plateNumber);
            break;
        case 'localize-vehicle':
            if (count($arguments) < 4) {
                echo "Please enter a valid <fleetId> <vehiclePlateNumber> <latitude> <longitude> [<altitude>]\n";
                break;
            }
            if (count($arguments) > 5) {
                echo "Error: 'localize-vehicle' command takes four or five arguments: <fleetId> <vehiclePlateNumber> <latitude> <longitude> [<altitude>]\n";
                break;
            }
            $fleetId = $arguments[0];
            $plateNumber = $arguments[1];
            $latitude = $arguments[2];
            $longitude = $arguments[3];
            $altitude = isset($arguments[4]) ? $arguments[4] : null;
            $command = new LocalizeVehicleCommand($fleetRepository, $vehicleRepository);
            $input = new ArrayInput([]);
            $output = new StreamOutput(STDOUT);
            $command->runCommand($input, $output, $fleetId, $plateNumber, $latitude, $longitude, $altitude);
            break;
        default:
            echo "Unknown command: $commandName\n";
            break;
    }

}
echo "Application exited.\n";