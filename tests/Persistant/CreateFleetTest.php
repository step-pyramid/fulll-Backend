<?php

namespace Tests\Persistant;

use Fulll\Console\Commands\Persistant\CreateFleet;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Domain\Persistant\Entities\User;
use Symfony\Component\Console\Command\Command;	
use Fulll\Domain\Persistant\Entities\Fleet;

class CreateFleetTest extends TestCase
{
    protected $entityManager;
    protected $input;
    protected $output;
    protected $user;
    protected $fleet;

    protected function setUp(): void
    {
        // Mock EntityManagerInterface, InputInterface, and OutputInterface
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        
        // Mock User entity
        $this->user = $this->createMock(User::class);

        $this->fleet = $this->createMock(Fleet::class);
        $this->fleet->method('getId')->willReturn('mocked_generated_id');
    }

    public function testRunCommand()
    {
    	// Expect persist and flush to be called on the entity manager
        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Expect writeln to be called on the output interface
        $this->output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('Fleet created successfully with ID: '.$this->fleet->getId()));

        // Create an instance of CreateUser with mocked dependencies
        $command = new CreateFleet($this->entityManager, $this->user, $this->fleet);

        // Call runCommand method and capture the result
        $result = $command->runCommand($this->input, $this->output);

        // Assert that the result is Command::SUCCESS
        $this->assertEquals(Command::SUCCESS, $result);

    }
}
