<?php

namespace App\Command;

use App\Entity\Stat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatsCommand extends Command
{
    protected static $defaultName = 'app:stats';

    public function __construct(private readonly string $ig_token, private readonly string $hc_token, private readonly EntityManagerInterface $em)
    {
        parent::__construct();
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
            $url = "https://graph.facebook.com/v9.0/17841406271014748?fields=id%2Cname%2Cfollowers_count&access_token=" . $this->ig_token;
            // TODO: gestion erreur HTTP != 200
            $json = file_get_contents($url);
            $data = json_decode($json, null, 512, JSON_THROW_ON_ERROR);

            /** @var Stat $stat */
            $stat = $this->em->getRepository(Stat::class)->findOneBy([
                'date' => new \DateTime()
            ]) ?? new Stat();

            $stat->name = "followers_count";
            $stat->value = $data->followers_count;
            $this->em->persist($stat);
            $this->em->flush();

            if ($this->hc_token) {
                file_get_contents('https://hc-ping.com/'.$this->hc_token);
            }
            $io->success('OK');
            return Command::SUCCESS;
        }
        catch (\Exception $e) {
            $io->error($e->getMessage());
            if ($this->hc_token) {
                file_get_contents('https://hc-ping.com/'.$this->hc_token.'/fail');
            }
            return Command::FAILURE;
        }
    }

}
