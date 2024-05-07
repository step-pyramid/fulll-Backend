<?php
namespace Fulll\Console\Commands\Memory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Infra\Memory\InMemoryUserRepository;
use Fulll\Infra\Memory\InMemoryFleetRepository;
use Fulll\Domain\Memory\User;
use Fulll\Domain\Memory\Fleet;

class CreateFleetCommand 
{
    private $userRepository;
    private $fleetRepository;

    public function __construct(InMemoryUserRepository $userRepository, InMemoryFleetRepository $fleetRepository)
    {
       
        $this->userRepository = $userRepository;
        $this->fleetRepository = $fleetRepository;
    }

    public function runCommand(InputInterface $input, OutputInterface $output, string $userId): int
    {
        $user = $this->userRepository->getById($userId);

        if($user !== null){

            $fleetId = uniqid('fleet_');

            $fleet = new Fleet($fleetId, $user);

            $this->fleetRepository->save($fleet);
            $output->writeln('Fleet created successfully, fleetId: '.$fleetId);
            return Command::SUCCESS;

        } else {
            
            $output->writeln("User does not exist, please create user first with following command: create-user, then 
                execute command: create-fleet with the userId from the previous command create-user");
            return Command::FAILURE;
        }

    }
}