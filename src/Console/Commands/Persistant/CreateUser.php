<?php
namespace Fulll\Console\Commands\Persistant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Fulll\Infra\InMemoryUserRepository;
use Fulll\Domain\Persistant\Entities\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;


class CreateUser
{

	private EntityManagerInterface $entityManager;
    private User $user;

    public function __construct(EntityManagerInterface $entityManager, User $user)
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
    }


    public function runCommand(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $userId = $this->user->getId(); 

        $output->writeln('User created successfully with ID: ' . $userId);

        
        return Command::SUCCESS;
    }

}