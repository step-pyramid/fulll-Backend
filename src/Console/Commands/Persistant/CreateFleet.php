<?php
namespace Fulll\Console\Commands\Persistant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Infra\InMemoryUserRepository;
use Fulll\Domain\Persistant\Entities\Fleet;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Fulll\Domain\Persistant\Entities\User;


class CreateFleet
{

	private EntityManagerInterface $entityManager;
    private User $user;
    private Fleet $fleet;

    public function __construct(EntityManagerInterface $entityManager, User $user, Fleet $fleet)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->fleet = $fleet;
    }


    public function runCommand(InputInterface $input, OutputInterface $output): int
    {
        
        $this->entityManager->persist($this->fleet);
        $this->entityManager->flush();

        $fleetId = $this->fleet->getId(); 

        $output->writeln('Fleet created successfully with ID: ' . $fleetId);

        
        return Command::SUCCESS;
    }

}