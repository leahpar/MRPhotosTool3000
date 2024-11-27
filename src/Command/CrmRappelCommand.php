<?php

namespace App\Command;

use App\Entity\Modele;
use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'crm:rappel',
    description: 'Command for CRM operations',
)]
class CrmRappelCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('test', 't', 'Test');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $modeles = $this->em->getRepository(Modele::class)->findAll();
        $now = new \DateTime();

        /** @var Modele $modele */
        foreach ($modeles as $modele) {

            if (!$modele->aSuivre) continue;

            $io->info("Modele: " . $modele->__toString());

            if ($modele->dateProchainContact && $modele->dateProchainContact < $now) {

                $io->info("à rappeler");

                // TODO: send email

            }


        }

    }

}
