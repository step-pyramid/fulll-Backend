<?php

namespace Tests\Memory;

use PHPUnit\Framework\TestCase;
use Fulll\Infra\Memory\InMemoryUserRepository;
use Fulll\Infra\Memory\InMemoryFleetRepository;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Fulll\Domain\Memory\User;
use Symfony\Component\Console\Command\Command;
use Fulll\Console\Commands\Memory\CreateFleetCommand;

class CreateFleetCommandTest extends TestCase
{
    private $userRepository;
    private $fleetRepository;
    private $createFleetCommand;

    protected function setUp(): void
    {
        $this->userRepository = new InMemoryUserRepository();
        $this->fleetRepository = new InMemoryFleetRepository();
        $this->createFleetCommand = new CreateFleetCommand($this->userRepository, $this->fleetRepository);
    }

    public function testRunCommandSuccess()
    {
        // Create a user and add to repository
        $user = new User('user_1');
        $this->userRepository->save($user);

        // Simulate console input and output
        $input = new ArrayInput(['userId' => 'user_1']);
        $output = new BufferedOutput();

        // Execute the command
        $result = $this->createFleetCommand->runCommand($input, $output, 'user_1');

        // Assert the command was successful
        $this->assertEquals(Command::SUCCESS, $result);

        // Get the output and assert the fleet was created successfully
        $outputContent = $output->fetch();
        $this->assertStringContainsString('Fleet created successfully, fleetId:', $outputContent);

        // Assert the fleet was saved in the repository
        $fleets = $this->fleetRepository->getAllFleets();
        $this->assertCount(1, $fleets);
        $this->assertStringStartsWith('fleet_', $fleets[0]->getId());
    }

    public function testRunCommandFailure()
    {
        // Simulate console input and output without creating a user first
        $input = new ArrayInput(['userId' => 'non_existing_user']);
        $output = new BufferedOutput();

        // Execute the command
        $result = $this->createFleetCommand->runCommand($input, $output, 'non_existing_user');

        // Assert the command failed
        $this->assertEquals(Command::FAILURE, $result);

        // Get the output and assert the error message was displayed
        $outputContent = $output->fetch();
        $this->assertStringContainsString('User does not exist, please create user first', $outputContent);
    }
}