<?php
namespace Fulll\Console\Commands\Memory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Domain\Memory\User;
use Fulll\Infra\Memory\InMemoryUserRepository;
use Fulll\Infra\Memory\InMemoryFleetRepository;

class CreateUserCommand
{

    private $userRepository;
    private $fleetRepository;

    public function __construct(InMemoryUserRepository $userRepository, InMemoryFleetRepository $fleetRepository)
    {
       
        $this->userRepository = $userRepository;
        $this->fleetRepository = $fleetRepository;
    }

    // Ensure the userId does not already exist
    private function generateUniqueUserId(): string
    {
        do {
            // Generate a new user ID with two digits after the underscore
            $userId = 'user_' . substr(uniqid(), -5);
        } while ($this->userRepository->getById($userId) !== null); // Check if the user ID already exists

        return $userId;
    }


    public function runCommand(InputInterface $input, OutputInterface $output): string
    {

        $userId = $this->generateUniqueUserId();

        $user = new User($userId);

        $this->userRepository->save($user);

        
        $output->writeln('User created successfully with ID: ' . $userId);

        
        return Command::SUCCESS;
    }

}