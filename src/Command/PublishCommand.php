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

    /**
     * @var PublishService
     */
    private PublishService $pubService;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * PublishCommand constructor.
     * @param PublishService $pubService
     * @param EntityManagerInterface $em
     */
    public function __construct(PublishService $pubService, EntityManagerInterface $em)
    {
        $this->pubService = $pubService;

        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            //$date = new \DateTime("friday next week");
            $date = null;
            $photo = $this->pubService->getPhotoToPublish($date);
            if ($photo) {
                $this->pubService->publishPhoto($photo);
                $this->em->persist($photo);
                $this->em->flush();
                $io->success($photo->getShooting()->getNom()." (".$photo->getFile().") publiée");
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
