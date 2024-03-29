<?php

use Fulll\Console\Commands\Persistant\CreateUser;
use Fulll\Console\Commands\Persistant\CreateFleet;
use Fulll\Console\Commands\Persistant\RegisterVehicle;
use Fulll\Console\Commands\Persistant\LocalizeVehicle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Command\Command;
use Fulll\Domain\Persistant\Entities\User;
use Fulll\Domain\Persistant\Entities\Fleet;

require_once __DIR__. '/bootstrap.php';

global $argc;

$entityManager = getEntityManager(); 

if (!isset($_SERVER['argv'][1])) {
    echo "No command provided.\n";
    exit(1);
}

$nameCommand = isset($_SERVER['argv'][1])? $_SERVER['argv'][1]: null;

switch ($nameCommand) {

    case 'create-user':
        if($argc > 2){
            echo "Too many commandline arguments.\n";
            exit(1);
        }
        $user = new User();
        $command = new CreateUser($entityManager, $user);
        $input = new ArrayInput([]);
        $output = new StreamOutput(STDOUT);
        $result = $command->runCommand($input, $output);
        break;

    case 'create-fleet':
        if($argc > 3){
            echo "Too many commandline arguments.\n";
            exit(1);
        }
        verifyInputCommandline($nameCommand);
        $userId = $_SERVER['argv'][2];
        $user = $entityManager->find(User::class, $userId);

        // Check if the user exists
        if (!$user) {
            echo "User with ID $userId not found.\n";
            exit(1);
        }
        $fleet = new Fleet($user);
        $command = new CreateFleet($entityManager, $user, $fleet);
        $input = new ArrayInput([]);
        $output = new StreamOutput(STDOUT);
        $result = $command->runCommand($input, $output);
        break;

    case 'register-vehicle':
        if($argc > 4){
            echo "Too many commandline arguments.\n";
            exit(1);
        }
        verifyInputCommandline($nameCommand);    
        $fleetId = $_SERVER['argv'][2];
        $vehiclePlateNumber = $_SERVER['argv'][3];
        $command = new RegisterVehicle($entityManager);
        $input = new ArrayInput([]);
        $output = new StreamOutput(STDOUT);
        $result = $command->runCommand($input, $output, $fleetId, $vehiclePlateNumber);
        break;

    case 'localize-vehicle':
        if($argc > 7){
            echo "Too many commandline arguments.\n";
            exit(1);
        }
        verifyInputCommandline($nameCommand);
        $fleetId = $_SERVER['argv'][2];
        $vehiclePlateNumber = $_SERVER['argv'][3];
        $latitude = $_SERVER['argv'][4];
        $longitude = $_SERVER['argv'][5];
        $altitude = isset($_SERVER['argv'][6]) ? $_SERVER['argv'][6]: null;
        if(isset($_SERVER['argv'][7])){
            echo "Too many commandline arguments.\n";
            exit(1);
        }
        $command = new LocalizeVehicle($entityManager);
        $input = new ArrayInput([]);
        $output = new StreamOutput(STDOUT);
        $result = $command->runCommand($input, $output, $fleetId, $vehiclePlateNumber, $latitude, $longitude, $altitude);
        break;
    default:
        echo "Unknown command: $nameCommand\n";
        break;
}


function handleCommandResult(int $result): void 
{
    if ($result === Command::SUCCESS) {
        echo "Command success.\n";
    } else {
        echo "Command unsuccessful.\n";
    }
}

function verifyInputCommandline(string $nameCommand){

    switch ($nameCommand) {

        case 'create-fleet':
        if (!isset($_SERVER['argv'][2])) {
            echo "No user ID provided.\n";
            exit(1);
        }
        break;
        case 'register-vehicle':
            if (!isset($_SERVER['argv'][2])) {
                echo "No fleet ID provided.\n";
                exit(1);
            }

            if (!isset($_SERVER['argv'][3])) {
                echo "No vehiclePlateNumber provided.\n";
                exit(1);
            }
        break;
        case 'localize-vehicle':

            if (!isset($_SERVER['argv'][2])) {
                echo "No fleet ID provided.\n";
                exit(1);
            }

            if (!isset($_SERVER['argv'][3])) {
                echo "No vehiclePlateNumber provided.\n";
                exit(1);
            }

            if (!isset($_SERVER['argv'][4])) {
                echo "No latitude provided.\n";
                exit(1);
            }

            if (!isset($_SERVER['argv'][5])) {
                echo "No longitude provided.\n";
                exit(1);
            }
        break;
        default:
        break;
    }
}