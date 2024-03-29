<?php

namespace Tests\Memory;

use PHPUnit\Framework\TestCase;
use Fulll\Infra\Memory\InMemoryUserRepository;
use Fulll\Infra\Memory\InMemoryFleetRepository;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Command\Command;
use Fulll\Console\Commands\Memory\CreateUserCommand;

class CreateUserCommandTest extends TestCase
{

    private $userRepository;
    private $fleetRepository;
    private $createUserCommand;

    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->createUserCommand = new CreateUserCommand($this->userRepository, $this->fleetRepository);
    }

    public function testRunCommand()
    {
        // Simulate console input and output
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        // Execute the command
        $result = $this->createUserCommand->runCommand($input, $output);

        // Assert the command was successful
        $this->assertEquals(Command::SUCCESS, $result);

        // Get the output and assert the user was created successfully
        $outputContent = $output->fetch();
        $this->assertStringContainsString('User created successfully with ID: user_', $outputContent);

        // Assert the user was saved in the repository
        $users = $this->userRepository->getAllUsers();
        $this->assertCount(1, $users);
        $this->assertStringStartsWith('user_', $users[0]->getId());
    }

}