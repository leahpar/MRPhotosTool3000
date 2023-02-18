<?php

namespace App\Command;

use App\Service\PublishService;
use App\Service\RssPublisherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PublishCommand extends Command
{
    protected static $defaultName = 'app:publish';

    public function __construct(
        private readonly PublishService $pubService,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addOption('test', 't', InputOption::VALUE_OPTIONAL, 'Test', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $testMode = ($input->getOption('test') !== false);
        try {
            $date = null;
            $photo = $this->pubService->getPhotoToPublish($date);
            if ($photo) {
                if (!$testMode) {
                    $this->pubService->publishPhoto($photo);
                    $this->em->persist($photo);
                    $this->em->flush();
                }
                $io->success($photo->getShooting()->getNom()." (".$photo->getFile().") à publier");
            }
            else {
                $io->success("Aucune photo à publier aujourd'hui");
            }
            return Command::SUCCESS;
        }
        catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }

}
