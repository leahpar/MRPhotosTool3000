<?php

namespace App\Command;

use App\Entity\Photo;
use App\Service\PhotoFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhotoCacheCommand extends Command
{
    protected static $defaultName = 'app:photo:cache';

    private PhotoFilterService $filterService;
    private EntityManagerInterface $em;

    /**
     * @param PhotoFilterService $filterService
     * @param EntityManagerInterface $em
     */
    public function __construct(PhotoFilterService $filterService, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->filterService = $filterService;
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

        $photos = $this->em->getRepository(Photo::class)->findAll();

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            foreach (['front', 'cover', 'mini'] as $filter) {
                $io->write("Photo ". str_pad($photo->getFile() . " ($filter) ...", 40));
                $t = microtime(true);
                $this->filterService->getFilteredPhoto($photo, $filter);
                $t = str_pad(round((microtime(true) - $t) * 1000), 5, ' ', STR_PAD_LEFT);
                $io->writeln("OK $t ms");
            }
        }

        $io->success('OK');

        return Command::SUCCESS;
    }

}

