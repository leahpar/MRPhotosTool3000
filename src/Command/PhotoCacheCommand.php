<?php

namespace App\Command;

use App\Entity\Galerie;
use App\Entity\Photo;
use App\Entity\Shooting;
use App\Service\PhotoFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:photo:cache',
    description: 'Cache photos',
)]
class PhotoCacheCommand extends Command
{
    public function __construct(
        private readonly PhotoFilterService $filterService,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Covers

        $photos = $this->em->getRepository(Photo::class)->findByGalerieIsCover();
        $io->info("Front covers");

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            $this->cachePhoto($io, $photo, 'front');
        }

        // Front

        $shootings = $this->em->getRepository(Shooting::class)->findAll();
        /** @var Shooting $shooting */
        foreach ($shootings as $shooting) {
            $io->info("Front site - " . $shooting->getNom());
            $photos = $this->em->getRepository(Photo::class)->findBy(
                ['shooting' => $shooting],
                ['file' => 'ASC'],
            );

            /** @var Photo $photo */
            foreach ($photos as $photo) {
                $this->cachePhoto($io, $photo, 'front');
            }
        }

        $io->success('OK');

        return Command::SUCCESS;
    }

    private function cachePhoto($io, Photo $photo, string $filter): void
    {
        $io->write("Photo ". str_pad($photo->getFile() . "...", 40));
        $t = microtime(true);
        try {
            $this->filterService->getFilteredPhoto($photo, $filter);
            $t = str_pad((string)(round((microtime(true) - $t) * 1000)), 5, ' ', STR_PAD_LEFT);
            $io->writeln("OK $t ms");
        }
        catch (\Exception $e) {
            $io->writeln("ERROR");
            $io->writeln($e->getMessage());
        }
    }

}

