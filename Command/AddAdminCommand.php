<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-admin',
    description: 'Adds a user with admin role',
)]
class AddAdminCommand extends Command
{
    private $entityManager;

     public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setDescription('Adds a user with admin role')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user to be promoted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $email = $input->getArgument('email');

        $userRepository= $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email'=> $email]);

        if(!$user)
        {
            $output->writeln('User not found');
            return Command::FAILURE;
        }

        $roles = $user->getRoles();

        if(!in_array('ROLE_ADMIN',$roles,true)){
            $roles[]= 'ROLE_ADMIN';
            $user->setRoles($roles);
            $this->entityManager->flush();
            $output->writeln('User has been granted admin role');
        }
        else{
            $output->writeln('User is already an admin');
        }

        return Command::SUCCESS;
    }
}
